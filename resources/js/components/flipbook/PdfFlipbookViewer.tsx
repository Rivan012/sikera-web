import { PageFlip } from 'page-flip/dist/js/page-flip.module.js';
import * as pdfjsLib from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import { useCallback, useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { ChevronLeftIcon, ChevronRightIcon, GridIcon } from '@/icons';
import { cn } from '@/utils';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

type PdfDocument = Awaited<ReturnType<typeof pdfjsLib.getDocument>['promise']>;
type LayoutMode = 'single' | 'spread';
type Status = 'loading' | 'error' | 'ready';

interface PdfFlipbookViewerProps {
    pdfUrl: string;
    title: string;
    downloadUrl?: string;
    version?: string;
}

const MIN_ZOOM = 0.75;
const MAX_ZOOM = 2;

function readErrorStatus(error: unknown): number | undefined {
    if (error && typeof error === 'object' && 'status' in error) {
        const status = (error as { status?: unknown }).status;
        return typeof status === 'number' ? status : undefined;
    }
    return undefined;
}

export default function PdfFlipbookViewer({ pdfUrl, title, downloadUrl, version }: PdfFlipbookViewerProps) {
    const rootRef = useRef<HTMLDivElement>(null);
    const viewportRef = useRef<HTMLDivElement>(null);
    const mountRef = useRef<HTMLDivElement>(null);
    const pdfDocRef = useRef<PdfDocument | null>(null);
    const loadingTaskRef = useRef<ReturnType<typeof pdfjsLib.getDocument> | null>(null);
    const pageFlipRef = useRef<PageFlip | null>(null);
    const renderedRef = useRef<Map<number, string>>(new Map());
    const renderingRef = useRef<Map<number, Promise<string | null>>>(new Map());
    const pageElsRef = useRef<HTMLDivElement[]>([]);
    const aspectRef = useRef(0.707);
    const totalRef = useRef(0);
    const currentRef = useRef(0);
    const preferredLayoutRef = useRef<LayoutMode>(
        typeof window !== 'undefined' && window.innerWidth >= 900 ? 'spread' : 'single',
    );
    const reducedMotionRef = useRef(
        typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    );

    const [status, setStatus] = useState<Status>('loading');
    const [loadingText, setLoadingText] = useState('Mengunduh dokumen PDF materi...');
    const [loadingPct, setLoadingPct] = useState(15);
    const [errorMessage, setErrorMessage] = useState('');
    const [reloadKey, setReloadKey] = useState(0);
    const [totalPages, setTotalPages] = useState(0);
    const [currentPage, setCurrentPage] = useState(0);
    const [orientation, setOrientation] = useState<'portrait' | 'landscape'>('portrait');
    const [isSpreadActive, setIsSpreadActive] = useState(false);
    const [canSpread, setCanSpread] = useState(false);
    const [zoom, setZoom] = useState(1);
    const [pageHeight, setPageHeight] = useState(560);
    const [thumbsOpen, setThumbsOpen] = useState(false);
    const [thumbUrls, setThumbUrls] = useState<Record<number, string>>({});
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [pageInput, setPageInput] = useState('1');

    const applyRenderedImage = useCallback((pageNumber: number, url: string) => {
        const pageEl = pageElsRef.current[pageNumber - 1];
        if (!pageEl) {
            return;
        }
        const img = pageEl.querySelector<HTMLImageElement>('.sikera-flip-page-img');
        const placeholder = pageEl.querySelector<HTMLElement>('.sikera-flip-page-placeholder');
        if (img) {
            img.src = url;
            img.classList.remove('hidden');
        }
        placeholder?.classList.add('hidden');
    }, []);

    const ensurePageRendered = useCallback(
        async (pageNumber: number): Promise<string | null> => {
            const pdfDoc = pdfDocRef.current;
            if (!pdfDoc || pageNumber < 1 || pageNumber > totalRef.current) {
                return null;
            }

            const cached = renderedRef.current.get(pageNumber);
            if (cached) {
                applyRenderedImage(pageNumber, cached);
                return cached;
            }

            const pending = renderingRef.current.get(pageNumber);
            if (pending) {
                return pending;
            }

            const task = (async () => {
                const page = await pdfDoc.getPage(pageNumber);
                const dpr = Math.min(window.devicePixelRatio || 1, 2);
                const unscaled = page.getViewport({ scale: 1 });
                const scale = Math.max(1.2, Math.min(2.6, (760 * dpr) / unscaled.width));
                const viewport = page.getViewport({ scale });

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d', { alpha: false });
                if (!ctx) {
                    return null;
                }
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                await page.render({ canvas: null, canvasContext: ctx, viewport }).promise;

                // Render ke blob image karena StPageFlip mengkloning node dan kanvas hasil kloning menjadi kosong.
                const objectUrl = await new Promise<string>((resolve) => {
                    canvas.toBlob(
                        (blob) => resolve(blob ? URL.createObjectURL(blob) : canvas.toDataURL('image/jpeg', 0.9)),
                        'image/webp',
                        0.9,
                    );
                });

                canvas.width = 0;
                canvas.height = 0;

                renderedRef.current.set(pageNumber, objectUrl);
                applyRenderedImage(pageNumber, objectUrl);
                setThumbUrls((prev) => ({ ...prev, [pageNumber]: objectUrl }));
                return objectUrl;
            })().finally(() => {
                renderingRef.current.delete(pageNumber);
            });

            renderingRef.current.set(pageNumber, task);
            return task;
        },
        [applyRenderedImage],
    );

    const preloadAround = useCallback(
        (zeroBasedIndex: number) => {
            const current = zeroBasedIndex + 1;
            [current, current + 1, current + 2, current - 1, current + 3, current - 2].forEach((num) => {
                if (num >= 1 && num <= totalRef.current) {
                    void ensurePageRendered(num);
                }
            });
        },
        [ensurePageRendered],
    );

    const syncFromFlip = useCallback((index: number) => {
        currentRef.current = index;
        setCurrentPage(index);
        setPageInput(String(index + 1));
        const flip = pageFlipRef.current;
        if (flip) {
            setOrientation(flip.getOrientation());
        }
    }, []);

    const rebuildFlipbook = useCallback(
        (startPageIndex: number) => {
            const mount = mountRef.current;
            const total = totalRef.current;
            if (!mount || total <= 0) {
                return;
            }

            if (pageFlipRef.current) {
                try {
                    pageFlipRef.current.destroy();
                } catch {
                    // Abaikan galat pembongkaran instance lama.
                }
                pageFlipRef.current = null;
            }
            mount.innerHTML = '';

            const containerWidth = Math.max(280, (viewportRef.current?.clientWidth || 720) - 32);
            const wideEnough = containerWidth >= 700 && total > 1;
            const useSpread = preferredLayoutRef.current === 'spread' && wideEnough;
            const maxPageWidth = useSpread ? Math.min(540, Math.floor(containerWidth / 2) - 8) : Math.min(680, containerWidth);
            const maxHeight = document.fullscreenElement
                ? Math.max(460, window.innerHeight - 190)
                : Math.max(400, Math.min(720, Math.round(window.innerHeight * 0.68)));

            let width = maxPageWidth;
            let height = Math.round(width / aspectRef.current);
            if (height > maxHeight) {
                height = maxHeight;
                width = Math.round(height * aspectRef.current);
            }
            width = Math.max(220, width);
            height = Math.max(260, height);

            setPageHeight(height);
            setCanSpread(wideEnough);
            setIsSpreadActive(useSpread);

            const host = document.createElement('div');
            host.className = 'sikera-flipbook-host';
            mount.appendChild(host);

            pageElsRef.current = [];
            for (let pageNumber = 1; pageNumber <= total; pageNumber++) {
                const pageEl = document.createElement('div');
                pageEl.className = 'sikera-flip-page';
                pageEl.dataset.density = (pageNumber === 1 || pageNumber === total) && useSpread ? 'hard' : 'soft';

                const inner = document.createElement('div');
                inner.className = 'sikera-flip-page-inner';

                const placeholder = document.createElement('div');
                placeholder.className = 'sikera-flip-page-placeholder';
                placeholder.textContent = `Memuat halaman ${pageNumber}...`;
                inner.appendChild(placeholder);

                const img = document.createElement('img');
                img.className = 'sikera-flip-page-img hidden';
                img.alt = `${title}, halaman ${pageNumber} dari ${total}`;
                img.width = width;
                img.height = height;
                img.draggable = false;
                inner.appendChild(img);

                const footer = document.createElement('span');
                footer.className = 'sikera-flip-page-number';
                footer.textContent = String(pageNumber);
                footer.setAttribute('aria-hidden', 'true');
                inner.appendChild(footer);

                pageEl.appendChild(inner);
                host.appendChild(pageEl);
                pageElsRef.current.push(pageEl);

                const cached = renderedRef.current.get(pageNumber);
                if (cached) {
                    applyRenderedImage(pageNumber, cached);
                }
            }

            const safeStart = Math.max(0, Math.min(total - 1, startPageIndex));
            const reduced = reducedMotionRef.current;

            const flip = new PageFlip(host, {
                width,
                height,
                size: 'fixed',
                minWidth: 220,
                maxWidth: 900,
                minHeight: 260,
                maxHeight: 1100,
                startPage: safeStart,
                drawShadow: true,
                maxShadowOpacity: 0.35,
                // StPageFlip menolak flippingTime <= 0, jadi mode gerak minimal memakai 1 ms.
                flippingTime: reduced ? 1 : 520,
                usePortrait: !useSpread,
                showCover: useSpread && total > 1,
                autoSize: true,
                mobileScrollSupport: true,
                clickEventForward: false,
                useMouseEvents: true,
                showPageCorners: !reduced,
                disableFlipByClick: false,
                swipeDistance: 30,
            });

            flip.loadFromHTML(host.querySelectorAll<HTMLElement>('.sikera-flip-page'));
            flip.on('flip', (event) => {
                const index = Number(event.data) || 0;
                syncFromFlip(index);
                preloadAround(index);
            });
            flip.on('changeOrientation', () => setOrientation(flip.getOrientation()));

            pageFlipRef.current = flip;
            syncFromFlip(safeStart);
        },
        [applyRenderedImage, preloadAround, syncFromFlip, title],
    );

    useEffect(() => {
        let cancelled = false;
        const rendered = renderedRef.current;

        const load = async () => {
            if (!pdfUrl) {
                setStatus('error');
                setErrorMessage('Tautan berkas PDF untuk subbab ini tidak tersedia.');
                return;
            }

            setStatus('loading');
            setLoadingText('Mengunduh dokumen PDF materi...');
            setLoadingPct(18);

            try {
                const separator = pdfUrl.includes('?') ? '&' : '?';
                const cacheTag = reloadKey > 0 ? `${version ?? '1'}-${reloadKey}-${Date.now()}` : (version ?? '1');
                const resolvedUrl = `${pdfUrl}${separator}v=${encodeURIComponent(cacheTag)}`;
                const loadingTask = pdfjsLib.getDocument({ url: resolvedUrl, withCredentials: true });
                loadingTaskRef.current = loadingTask;
                loadingTask.onProgress = (progress: { loaded: number; total: number }) => {
                    if (progress.total > 0 && !cancelled) {
                        setLoadingPct(Math.round((progress.loaded / progress.total) * 55) + 15);
                    }
                };

                const pdfDoc = await loadingTask.promise;
                if (cancelled) {
                    return;
                }

                pdfDocRef.current = pdfDoc;
                totalRef.current = pdfDoc.numPages;
                setTotalPages(pdfDoc.numPages);

                if (pdfDoc.numPages <= 0) {
                    setStatus('error');
                    setErrorMessage('Dokumen PDF ini tidak memiliki halaman yang dapat ditampilkan.');
                    return;
                }

                const firstPage = await pdfDoc.getPage(1);
                const base = firstPage.getViewport({ scale: 1 });
                aspectRef.current = Math.max(0.52, Math.min(1.85, base.width / base.height || 0.707));

                if (pdfDoc.numPages === 1) {
                    preferredLayoutRef.current = 'single';
                }

                const initialLimit = Math.min(pdfDoc.numPages, 3);
                for (let p = 1; p <= initialLimit; p++) {
                    if (cancelled) {
                        return;
                    }
                    setLoadingText(`Menyiapkan halaman ${p} dari ${pdfDoc.numPages}...`);
                    setLoadingPct(78 + Math.round((p / initialLimit) * 20));
                    await ensurePageRendered(p);
                }

                if (cancelled) {
                    return;
                }
                setStatus('ready');
            } catch (error) {
                if (cancelled) {
                    return;
                }
                const code = readErrorStatus(error);
                setStatus('error');
                if (code === 403) {
                    setErrorMessage('Akses PDF terkunci. Selesaikan Pre-Test modul terlebih dahulu.');
                } else if (code === 404) {
                    setErrorMessage('Berkas PDF tidak ditemukan di server. Hubungi pengelola modul.');
                } else {
                    setErrorMessage('Dokumen PDF gagal dimuat ke pembaca flipbook. Coba muat ulang atau buka berkas PDF secara langsung.');
                }
            }
        };

        void load();

        return () => {
            cancelled = true;
            if (pageFlipRef.current) {
                try {
                    pageFlipRef.current.destroy();
                } catch {
                    // Abaikan galat pembongkaran saat unmount.
                }
                pageFlipRef.current = null;
            }
            rendered.forEach((url) => {
                if (url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
            });
            rendered.clear();
            renderingRef.current.clear();
            setThumbUrls({});
            pdfDocRef.current = null;
            if (loadingTaskRef.current) {
                void loadingTaskRef.current.destroy();
                loadingTaskRef.current = null;
            }
        };
    }, [pdfUrl, version, reloadKey, ensurePageRendered]);

    useEffect(() => {
        if (status !== 'ready') {
            return;
        }
        rebuildFlipbook(0);
        preloadAround(0);
    }, [status, rebuildFlipbook, preloadAround]);

    useEffect(() => {
        if (status !== 'ready') {
            return undefined;
        }
        let timer: number | undefined;
        const onResize = () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => rebuildFlipbook(currentRef.current), 220);
        };
        window.addEventListener('resize', onResize);
        return () => {
            window.clearTimeout(timer);
            window.removeEventListener('resize', onResize);
        };
    }, [status, rebuildFlipbook]);

    useEffect(() => {
        const onFullscreenChange = () => {
            setIsFullscreen(document.fullscreenElement === rootRef.current);
            window.setTimeout(() => {
                if (totalRef.current > 0 && pdfDocRef.current) {
                    rebuildFlipbook(currentRef.current);
                }
            }, 120);
        };
        document.addEventListener('fullscreenchange', onFullscreenChange);
        return () => document.removeEventListener('fullscreenchange', onFullscreenChange);
    }, [rebuildFlipbook]);

    const goPrev = useCallback(() => {
        const flip = pageFlipRef.current;
        if (!flip || currentRef.current <= 0) {
            return;
        }
        if (reducedMotionRef.current) {
            flip.turnToPrevPage();
            syncFromFlip(flip.getCurrentPageIndex());
        } else {
            flip.flipPrev();
        }
    }, [syncFromFlip]);

    const goNext = useCallback(() => {
        const flip = pageFlipRef.current;
        if (!flip || currentRef.current >= totalRef.current - 1) {
            return;
        }
        if (reducedMotionRef.current) {
            flip.turnToNextPage();
            syncFromFlip(flip.getCurrentPageIndex());
        } else {
            flip.flipNext();
        }
    }, [syncFromFlip]);

    const goToPage = useCallback(
        (zeroBasedIndex: number, instant = false) => {
            const flip = pageFlipRef.current;
            if (!flip) {
                return;
            }
            const target = Math.max(0, Math.min(totalRef.current - 1, zeroBasedIndex));
            preloadAround(target);
            if (instant || reducedMotionRef.current) {
                flip.turnToPage(target);
                syncFromFlip(target);
            } else {
                flip.flip(target);
            }
        },
        [preloadAround, syncFromFlip],
    );

    useEffect(() => {
        if (status !== 'ready') {
            return undefined;
        }
        const onKeyDown = (event: KeyboardEvent) => {
            const target = event.target as HTMLElement | null;
            const tag = target?.tagName?.toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select' || target?.isContentEditable) {
                return;
            }
            if (document.querySelector('[role="dialog"]')) {
                return;
            }
            if (event.key === 'ArrowRight' || event.key === 'PageDown') {
                event.preventDefault();
                goNext();
            } else if (event.key === 'ArrowLeft' || event.key === 'PageUp') {
                event.preventDefault();
                goPrev();
            } else if (event.key === 'Home') {
                event.preventDefault();
                goToPage(0);
            } else if (event.key === 'End') {
                event.preventDefault();
                goToPage(totalRef.current - 1);
            }
        };
        document.addEventListener('keydown', onKeyDown);
        return () => document.removeEventListener('keydown', onKeyDown);
    }, [status, goNext, goPrev, goToPage]);

    const setLayout = (mode: LayoutMode) => {
        if (mode === 'spread' && totalRef.current <= 1) {
            return;
        }
        if (preferredLayoutRef.current === mode && (mode === 'spread') === isSpreadActive) {
            return;
        }
        preferredLayoutRef.current = mode;
        rebuildFlipbook(currentRef.current);
    };

    const changeZoom = (next: number) => {
        setZoom(Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, Math.round(next * 100) / 100)));
    };

    const toggleFullscreen = async () => {
        try {
            if (!document.fullscreenElement) {
                await rootRef.current?.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        } catch {
            // API layar penuh tidak tersedia atau diblokir peramban.
        }
    };

    const toggleThumbnails = () => {
        setThumbsOpen((open) => {
            const next = !open;
            if (next) {
                const limit = Math.min(totalRef.current, 24);
                for (let p = 1; p <= limit; p++) {
                    void ensurePageRendered(p);
                }
            }
            return next;
        });
    };

    const submitPageInput = () => {
        const parsed = parseInt(pageInput, 10);
        if (!Number.isNaN(parsed)) {
            goToPage(parsed - 1);
        } else {
            setPageInput(String(currentPage + 1));
        }
    };

    const humanPage = currentPage + 1;
    const isTwoPageSpread = orientation === 'landscape' && totalPages > 1 && humanPage > 1 && humanPage < totalPages;
    const rangeLabel = isTwoPageSpread
        ? `Halaman ${humanPage} sampai ${Math.min(totalPages, humanPage + 1)} dari ${totalPages}`
        : `Halaman ${humanPage} dari ${totalPages}`;
    const isAtEnd = isTwoPageSpread
        ? humanPage + 1 >= totalPages
        : currentPage >= totalPages - 1;
    const extraZoomSpace = zoom > 1 ? Math.ceil(pageHeight * (zoom - 1)) : 0;

    const toggleBtn = (active: boolean) =>
        cn(
            'inline-flex min-h-11 items-center justify-center rounded-lg px-3 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:cursor-not-allowed disabled:opacity-50 dark:focus-visible:outline-teal-400',
            active
                ? 'bg-teal-700 text-white dark:bg-teal-400 dark:text-slate-950'
                : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700',
        );

    return (
        <section
            ref={rootRef}
            aria-label={`Pembaca flipbook PDF: ${title}`}
            className="sikera-flipbook-root flex w-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-parchment dark:border-slate-700 dark:bg-slate-900"
        >
            {status === 'loading' && (
                <div role="status" className="flex min-h-80 flex-col items-center justify-center gap-4 p-8 text-center">
                    <p className="text-sm font-semibold text-slate-800 dark:text-slate-100">{loadingText}</p>
                    <div className="h-2 w-full max-w-xs overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700" aria-hidden="true">
                        <div className="h-full rounded-full bg-teal-700 transition-[width] duration-300 dark:bg-teal-400" style={{ width: `${Math.max(5, Math.min(100, loadingPct))}%` }} />
                    </div>
                </div>
            )}

            {status === 'error' && (
                <div role="alert" className="flex min-h-80 flex-col items-center justify-center gap-4 p-8 text-center">
                    <p className="max-w-md text-sm font-medium text-rose-900 dark:text-rose-200">{errorMessage}</p>
                    <div className="flex flex-wrap items-center justify-center gap-2">
                        <Button onClick={() => setReloadKey((key) => key + 1)}>Muat ulang PDF</Button>
                        {downloadUrl && (
                            <a
                                href={downloadUrl}
                                target="_blank"
                                rel="noopener"
                                className="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                            >
                                Buka berkas PDF
                            </a>
                        )}
                    </div>
                </div>
            )}

            <div className={cn('flex-col', status === 'ready' ? 'flex' : 'hidden')}>
                <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                    <div className="flex items-center gap-1 rounded-lg border border-slate-200 p-0.5 dark:border-slate-700" role="group" aria-label="Tata letak halaman">
                        <button type="button" className={toggleBtn(!isSpreadActive)} aria-pressed={!isSpreadActive} onClick={() => setLayout('single')}>
                            1 Halaman
                        </button>
                        <button
                            type="button"
                            className={toggleBtn(isSpreadActive)}
                            aria-pressed={isSpreadActive}
                            disabled={!canSpread}
                            onClick={() => setLayout('spread')}
                        >
                            2 Halaman
                        </button>
                    </div>

                    <div className="flex items-center gap-1" role="group" aria-label="Perbesaran">
                        <Button variant="outline" size="icon" onClick={() => changeZoom(zoom - 0.25)} disabled={zoom <= MIN_ZOOM} aria-label="Perkecil tampilan">
                            <span aria-hidden="true" className="text-base font-bold">-</span>
                        </Button>
                        <button
                            type="button"
                            onClick={() => changeZoom(1)}
                            aria-label="Kembalikan perbesaran ke 100 persen"
                            className="inline-flex min-h-11 min-w-16 items-center justify-center rounded-lg text-xs font-semibold text-slate-800 tabular-nums hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-700"
                        >
                            {Math.round(zoom * 100)}%
                        </button>
                        <Button variant="outline" size="icon" onClick={() => changeZoom(zoom + 0.25)} disabled={zoom >= MAX_ZOOM} aria-label="Perbesar tampilan">
                            <span aria-hidden="true" className="text-base font-bold">+</span>
                        </Button>
                    </div>

                    <div className="flex items-center gap-1">
                        <Button variant="outline" size="sm" onClick={toggleThumbnails} aria-expanded={thumbsOpen} aria-controls="sikera-flipbook-thumbs">
                            <GridIcon className="size-4" />
                            <span className="hidden sm:inline">Daftar Halaman</span>
                        </Button>
                        <Button variant="outline" size="sm" onClick={toggleFullscreen} aria-pressed={isFullscreen}>
                            {isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'}
                        </Button>
                    </div>
                </div>

                {thumbsOpen && (
                    <div id="sikera-flipbook-thumbs" className="border-b border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-800">
                        <div className="flex gap-2 overflow-x-auto pb-1">
                            {Array.from({ length: totalPages }, (_, idx) => {
                                const active = isTwoPageSpread ? idx === currentPage || idx === currentPage + 1 : idx === currentPage;
                                const url = thumbUrls[idx + 1];
                                return (
                                    <button
                                        key={idx}
                                        type="button"
                                        onClick={() => goToPage(idx)}
                                        aria-label={`Buka halaman ${idx + 1}`}
                                        aria-current={active ? 'page' : undefined}
                                        className={cn(
                                            'flex shrink-0 flex-col items-center gap-1.5 rounded-lg border bg-white p-2 text-xs font-semibold text-slate-800 transition-colors hover:border-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-teal-400',
                                            active
                                                ? 'border-teal-700 ring-2 ring-teal-700 dark:border-teal-400 dark:ring-teal-400'
                                                : 'border-slate-200 dark:border-slate-700',
                                        )}
                                    >
                                        <span className="flex h-24 w-18 items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-[11px] font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                            {url ? <img src={url} alt="" width={72} height={96} className="h-full w-full object-contain" /> : idx + 1}
                                        </span>
                                        <span>Hal. {idx + 1}</span>
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                )}

                <div ref={viewportRef} className="relative w-full overflow-x-auto px-4 py-6">
                    <div style={{ transform: `scale(${zoom})`, transformOrigin: 'top center', marginBottom: extraZoomSpace }}>
                        <div ref={mountRef} className="flex justify-center" />
                    </div>
                </div>

                <div className="flex flex-col gap-3 border-t border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-800">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <Button variant="outline" size="sm" onClick={goPrev} disabled={currentPage <= 0} aria-label="Halaman sebelumnya">
                            <ChevronLeftIcon />
                            <span className="hidden sm:inline">Sebelumnya</span>
                        </Button>

                        <div className="flex items-center gap-2 text-xs font-semibold text-slate-800 dark:text-slate-100">
                            <label htmlFor="sikera-flipbook-page-input" className="sr-only">
                                Lompat ke halaman
                            </label>
                            <input
                                id="sikera-flipbook-page-input"
                                type="number"
                                inputMode="numeric"
                                min={1}
                                max={totalPages || 1}
                                value={pageInput}
                                onChange={(event) => setPageInput(event.target.value)}
                                onBlur={submitPageInput}
                                onKeyDown={(event) => {
                                    if (event.key === 'Enter') {
                                        event.preventDefault();
                                        submitPageInput();
                                    }
                                }}
                                className="min-h-11 w-16 rounded-lg border border-slate-300 bg-white px-2 text-center text-sm tabular-nums text-slate-900 focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            />
                            <span>dari {totalPages}</span>
                        </div>

                        <Button variant="outline" size="sm" onClick={goNext} disabled={isAtEnd} aria-label="Halaman berikutnya">
                            <span className="hidden sm:inline">Berikutnya</span>
                            <ChevronRightIcon />
                        </Button>
                    </div>

                    <div className="flex flex-col gap-1">
                        <label htmlFor="sikera-flipbook-scrubber" className="sr-only">
                            Geser untuk berpindah halaman
                        </label>
                        <input
                            id="sikera-flipbook-scrubber"
                            type="range"
                            min={1}
                            max={totalPages || 1}
                            value={humanPage}
                            onChange={(event) => {
                                const parsed = parseInt(event.target.value, 10);
                                if (!Number.isNaN(parsed)) {
                                    goToPage(parsed - 1, true);
                                }
                            }}
                            className="h-11 w-full accent-teal-700 dark:accent-teal-400"
                        />
                        <p aria-live="polite" className="text-center text-xs font-medium text-slate-600 dark:text-slate-300">
                            {rangeLabel}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
