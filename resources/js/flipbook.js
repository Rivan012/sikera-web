import * as pdfjsLib from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import { PageFlip } from 'page-flip/dist/js/page-flip.module.js';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

class SikeraPdfFlipbook {
    constructor(root) {
        this.root = root;
        this.pdfUrl = root.dataset.pdfUrl || '';
        this.docTitle = root.dataset.title || 'Materi PDF';

        this.loadingEl = root.querySelector('[data-flipbook-loading]');
        this.loadingTextEl = root.querySelector('[data-flipbook-loading-text]');
        this.loadingBarEl = root.querySelector('[data-flipbook-loading-bar]');
        this.errorEl = root.querySelector('[data-flipbook-error]');
        this.errorMessageEl = root.querySelector('[data-flipbook-error-message]');
        this.retryBtn = root.querySelector('[data-flipbook-retry]');
        this.viewerEl = root.querySelector('[data-flipbook-viewer]');
        this.viewportEl = root.querySelector('[data-flipbook-viewport]');
        this.zoomLayerEl = root.querySelector('[data-flipbook-zoom-layer]');
        this.bookMountEl = root.querySelector('[data-flipbook-mount]');

        this.prevBtn = root.querySelector('[data-flipbook-prev]');
        this.nextBtn = root.querySelector('[data-flipbook-next]');
        this.pageInput = root.querySelector('[data-flipbook-page-input]');
        this.pageTotalEl = root.querySelector('[data-flipbook-page-total]');
        this.pageRangeEl = root.querySelector('[data-flipbook-page-range]');
        this.scrubberEl = root.querySelector('[data-flipbook-scrubber]');
        this.announceEl = root.querySelector('[data-flipbook-announce]');

        this.zoomOutBtn = root.querySelector('[data-flipbook-zoom-out]');
        this.zoomInBtn = root.querySelector('[data-flipbook-zoom-in]');
        this.zoomResetBtn = root.querySelector('[data-flipbook-zoom-reset]');
        this.zoomLabelEl = root.querySelector('[data-flipbook-zoom-label]');

        this.layoutSingleBtn = root.querySelector('[data-flipbook-layout="single"]');
        this.layoutSpreadBtn = root.querySelector('[data-flipbook-layout="spread"]');
        this.fullscreenBtn = root.querySelector('[data-flipbook-fullscreen]');
        this.thumbsToggleBtn = root.querySelector('[data-flipbook-thumbs-toggle]');
        this.thumbsPanelEl = root.querySelector('[data-flipbook-thumbs-panel]');
        this.thumbsGridEl = root.querySelector('[data-flipbook-thumbs-grid]');

        this.pdfDoc = null;
        this.pageFlip = null;
        this.totalPages = 0;
        this.currentPage = 0; // 0-indexed
        this.zoom = 1;
        this.lastPageHeight = 560;
        this.aspectRatio = 0.707;
        this.preferredLayout = window.innerWidth >= 900 ? 'spread' : 'single';
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        this.renderedPages = new Map(); // 1-based pageNumber -> objectUrl
        this.renderingPages = new Map(); // 1-based pageNumber -> Promise
        this.pageElements = [];
        this.thumbButtons = [];
        this.resizeTimer = null;

        this.bindStaticEvents();
        this.load();
    }

    bindStaticEvents() {
        this.retryBtn?.addEventListener('click', () => this.load());

        this.prevBtn?.addEventListener('click', () => this.goPrev());
        this.nextBtn?.addEventListener('click', () => this.goNext());

        this.pageInput?.addEventListener('change', () => {
            const parsed = parseInt(this.pageInput.value, 10);
            if (!Number.isNaN(parsed)) {
                this.goToPage(parsed - 1);
            } else {
                this.syncControls();
            }
        });

        this.pageInput?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                this.pageInput.blur();
                const parsed = parseInt(this.pageInput.value, 10);
                if (!Number.isNaN(parsed)) {
                    this.goToPage(parsed - 1);
                }
            }
        });

        this.scrubberEl?.addEventListener('input', () => {
            const parsed = parseInt(this.scrubberEl.value, 10);
            if (!Number.isNaN(parsed)) {
                this.goToPage(parsed - 1, true);
            }
        });

        this.zoomOutBtn?.addEventListener('click', () => this.setZoom(this.zoom - 0.25));
        this.zoomInBtn?.addEventListener('click', () => this.setZoom(this.zoom + 0.25));
        this.zoomResetBtn?.addEventListener('click', () => this.setZoom(1));

        this.layoutSingleBtn?.addEventListener('click', () => this.setLayout('single'));
        this.layoutSpreadBtn?.addEventListener('click', () => this.setLayout('spread'));

        this.fullscreenBtn?.addEventListener('click', () => this.toggleFullscreen());
        document.addEventListener('fullscreenchange', () => this.syncFullscreenButton());

        this.thumbsToggleBtn?.addEventListener('click', () => this.toggleThumbnails());

        document.addEventListener('keydown', (event) => this.handleKeydown(event));

        window.addEventListener('resize', () => {
            window.clearTimeout(this.resizeTimer);
            this.resizeTimer = window.setTimeout(() => {
                if (this.pdfDoc && this.totalPages > 0) {
                    this.rebuildFlipbook(this.currentPage);
                }
            }, 220);
        });

        window.addEventListener('beforeunload', () => this.cleanupUrls());
    }

    showLoading(message, percent = 15) {
        this.loadingEl?.classList.remove('hidden');
        this.loadingEl?.classList.add('flex');
        this.errorEl?.classList.add('hidden');
        this.errorEl?.classList.remove('flex');
        this.viewerEl?.classList.add('hidden');
        this.viewerEl?.classList.remove('flex');
        if (this.loadingTextEl) {
            this.loadingTextEl.textContent = message;
        }
        if (this.loadingBarEl) {
            this.loadingBarEl.style.width = `${Math.max(5, Math.min(100, percent))}%`;
        }
    }

    showError(message) {
        this.loadingEl?.classList.add('hidden');
        this.loadingEl?.classList.remove('flex');
        this.viewerEl?.classList.add('hidden');
        this.viewerEl?.classList.remove('flex');
        this.errorEl?.classList.remove('hidden');
        this.errorEl?.classList.add('flex');
        if (this.errorMessageEl) {
            this.errorMessageEl.textContent = message;
        }
    }

    showViewer() {
        this.loadingEl?.classList.add('hidden');
        this.loadingEl?.classList.remove('flex');
        this.errorEl?.classList.add('hidden');
        this.errorEl?.classList.remove('flex');
        this.viewerEl?.classList.remove('hidden');
        this.viewerEl?.classList.add('flex');
    }

    async load() {
        if (!this.pdfUrl) {
            this.showError('Tautan berkas PDF untuk subbab ini tidak tersedia.');
            return;
        }

        this.showLoading('Mengunduh dokumen PDF materi...', 18);

        try {
            const loadingTask = pdfjsLib.getDocument({
                url: this.pdfUrl,
                withCredentials: true,
            });

            loadingTask.onProgress = (progress) => {
                if (progress.total && progress.total > 0) {
                    const pct = Math.round((progress.loaded / progress.total) * 55) + 15;
                    this.showLoading('Mengunduh dokumen PDF materi...', pct);
                }
            };

            this.pdfDoc = await loadingTask.promise;
            this.totalPages = this.pdfDoc.numPages;

            if (this.totalPages <= 0) {
                this.showError('Dokumen PDF ini tidak memiliki halaman yang dapat ditampilkan.');
                return;
            }

            const firstPage = await this.pdfDoc.getPage(1);
            const baseViewport = firstPage.getViewport({ scale: 1 });
            const rawAspect = baseViewport.width / baseViewport.height;
            this.aspectRatio = Math.max(0.52, Math.min(1.85, rawAspect || 0.707));

            if (this.totalPages === 1) {
                this.preferredLayout = 'single';
            }

            this.showLoading(`Menyiapkan halaman 1 dari ${this.totalPages}...`, 78);

            // Render initial pages before mounting so the cover/first spread paints immediately.
            const initialLimit = Math.min(this.totalPages, 3);
            for (let p = 1; p <= initialLimit; p++) {
                await this.ensurePageRendered(p);
                const pct = 78 + Math.round((p / initialLimit) * 20);
                this.showLoading(`Menyiapkan halaman ${p} dari ${this.totalPages}...`, pct);
            }

            this.showViewer();
            this.buildThumbnailsList();
            this.rebuildFlipbook(0);
            this.preloadAround(0);
        } catch (error) {
            const status = error?.status;
            if (status === 403) {
                this.showError('Akses PDF terkunci. Selesaikan Pre-Test modul terlebih dahulu.');
            } else if (status === 404) {
                this.showError('Berkas PDF tidak ditemukan di server. Hubungi pengelola modul.');
            } else {
                this.showError('Dokumen PDF gagal dimuat ke pembaca flipbook. Coba muat ulang atau buka berkas PDF secara langsung.');
            }
        }
    }

    computeStageMetrics() {
        const containerWidth = Math.max(280, (this.viewportEl?.clientWidth || 720) - 32);
        const isWideEnough = containerWidth >= 700 && this.totalPages > 1;
        const useSpread = this.preferredLayout === 'spread' && isWideEnough;

        const maxPageWidth = useSpread
            ? Math.min(540, Math.floor(containerWidth / 2) - 8)
            : Math.min(680, containerWidth);

        const maxHeight = document.fullscreenElement
            ? Math.max(460, window.innerHeight - 190)
            : Math.max(400, Math.min(720, Math.round(window.innerHeight * 0.68)));

        let pageWidth = maxPageWidth;
        let pageHeight = Math.round(pageWidth / this.aspectRatio);

        if (pageHeight > maxHeight) {
            pageHeight = maxHeight;
            pageWidth = Math.round(pageHeight * this.aspectRatio);
        }

        pageWidth = Math.max(220, pageWidth);
        pageHeight = Math.max(260, pageHeight);

        return {
            useSpread,
            pageWidth,
            pageHeight,
        };
    }

    rebuildFlipbook(startPageIndex = 0) {
        if (!this.bookMountEl || this.totalPages <= 0) {
            return;
        }

        if (this.pageFlip) {
            try {
                this.pageFlip.destroy();
            } catch {
                // Ignore teardown error from previous instance.
            }
            this.pageFlip = null;
        }

        this.bookMountEl.innerHTML = '';

        const { useSpread, pageWidth, pageHeight } = this.computeStageMetrics();
        this.lastPageHeight = pageHeight;
        const host = document.createElement('div');
        host.className = 'sikera-flipbook-host';
        this.bookMountEl.appendChild(host);

        this.pageElements = [];
        for (let pageNumber = 1; pageNumber <= this.totalPages; pageNumber++) {
            const pageEl = document.createElement('div');
            pageEl.className = 'sikera-flip-page';
            pageEl.dataset.density = (pageNumber === 1 || pageNumber === this.totalPages) && useSpread ? 'hard' : 'soft';
            pageEl.dataset.pageNumber = String(pageNumber);

            const inner = document.createElement('div');
            inner.className = 'sikera-flip-page-inner';

            const placeholder = document.createElement('div');
            placeholder.className = 'sikera-flip-page-placeholder';
            placeholder.textContent = `Memuat halaman ${pageNumber}...`;
            inner.appendChild(placeholder);

            const img = document.createElement('img');
            img.className = 'sikera-flip-page-img hidden';
            img.alt = `${this.docTitle}, halaman ${pageNumber} dari ${this.totalPages}`;
            img.width = pageWidth;
            img.height = pageHeight;
            img.draggable = false;
            inner.appendChild(img);

            const footer = document.createElement('span');
            footer.className = 'sikera-flip-page-number';
            footer.textContent = String(pageNumber);
            footer.setAttribute('aria-hidden', 'true');
            inner.appendChild(footer);

            pageEl.appendChild(inner);
            host.appendChild(pageEl);
            this.pageElements.push(pageEl);

            if (this.renderedPages.has(pageNumber)) {
                this.applyRenderedImage(pageNumber, this.renderedPages.get(pageNumber));
            }
        }

        const safeStart = Math.max(0, Math.min(this.totalPages - 1, startPageIndex));

        this.pageFlip = new PageFlip(host, {
            width: pageWidth,
            height: pageHeight,
            size: 'fixed',
            minWidth: 220,
            maxWidth: 900,
            minHeight: 260,
            maxHeight: 1100,
            startPage: safeStart,
            drawShadow: true,
            maxShadowOpacity: 0.35,
            flippingTime: this.reducedMotion ? 1 : 520,
            usePortrait: !useSpread,
            showCover: useSpread && this.totalPages > 1,
            autoSize: true,
            mobileScrollSupport: true,
            clickEventForward: false,
            useMouseEvents: true,
            showPageCorners: !this.reducedMotion,
            disableFlipByClick: false,
            swipeDistance: 30,
        });

        this.pageFlip.loadFromHTML(host.querySelectorAll('.sikera-flip-page'));

        this.pageFlip.on('flip', (event) => {
            this.currentPage = Number(event.data) || 0;
            this.syncControls();
            this.preloadAround(this.currentPage);
        });

        this.currentPage = safeStart;
        this.applyZoom();
        this.syncLayoutButtons(useSpread);
        this.syncControls();
    }

    async ensurePageRendered(pageNumber) {
        if (pageNumber < 1 || pageNumber > this.totalPages || !this.pdfDoc) {
            return null;
        }

        if (this.renderedPages.has(pageNumber)) {
            const url = this.renderedPages.get(pageNumber);
            this.applyRenderedImage(pageNumber, url);
            return url;
        }

        if (this.renderingPages.has(pageNumber)) {
            return this.renderingPages.get(pageNumber);
        }

        const renderPromise = (async () => {
            const page = await this.pdfDoc.getPage(pageNumber);
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            const targetWidth = 760 * dpr;
            const unscaled = page.getViewport({ scale: 1 });
            const scale = Math.max(1.2, Math.min(2.6, targetWidth / unscaled.width));
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d', { alpha: false });
            canvas.width = Math.ceil(viewport.width);
            canvas.height = Math.ceil(viewport.height);

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            await page.render({ canvasContext: ctx, viewport }).promise;

            const objectUrl = await new Promise((resolve) => {
                canvas.toBlob(
                    (blob) => {
                        if (blob) {
                            resolve(URL.createObjectURL(blob));
                        } else {
                            resolve(canvas.toDataURL('image/jpeg', 0.9));
                        }
                    },
                    'image/webp',
                    0.9
                );
            });

            canvas.width = 0;
            canvas.height = 0;

            this.renderedPages.set(pageNumber, objectUrl);
            this.applyRenderedImage(pageNumber, objectUrl);
            this.applyThumbnailImage(pageNumber, objectUrl);
            return objectUrl;
        })().finally(() => {
            this.renderingPages.delete(pageNumber);
        });

        this.renderingPages.set(pageNumber, renderPromise);
        return renderPromise;
    }

    applyRenderedImage(pageNumber, url) {
        const pageEl = this.pageElements[pageNumber - 1];
        if (!pageEl || !url) {
            return;
        }

        const img = pageEl.querySelector('.sikera-flip-page-img');
        const placeholder = pageEl.querySelector('.sikera-flip-page-placeholder');
        if (img) {
            img.src = url;
            img.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
    }

    preloadAround(zeroBasedIndex) {
        const current = zeroBasedIndex + 1;
        const targets = [current, current + 1, current + 2, current - 1, current + 3, current - 2];
        for (const num of targets) {
            if (num >= 1 && num <= this.totalPages) {
                this.ensurePageRendered(num);
            }
        }
    }

    buildThumbnailsList() {
        if (!this.thumbsGridEl) {
            return;
        }

        this.thumbsGridEl.innerHTML = '';
        this.thumbButtons = [];

        for (let pageNumber = 1; pageNumber <= this.totalPages; pageNumber++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.thumbPage = String(pageNumber);
            btn.className = 'sikera-thumb-btn flex shrink-0 flex-col items-center gap-1.5 rounded-lg border border-slate-200 bg-white p-2 text-xs font-semibold text-slate-800 transition-colors hover:border-teal-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-teal-400';
            btn.setAttribute('aria-label', `Buka halaman ${pageNumber}`);

            const preview = document.createElement('div');
            preview.className = 'flex h-24 w-18 items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-[11px] font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300';

            const img = document.createElement('img');
            img.className = 'hidden h-full w-full object-contain';
            img.alt = '';
            img.width = 72;
            img.height = 96;
            preview.appendChild(img);

            const numFallback = document.createElement('span');
            numFallback.className = 'sikera-thumb-fallback';
            numFallback.textContent = String(pageNumber);
            preview.appendChild(numFallback);

            const label = document.createElement('span');
            label.textContent = `Hal. ${pageNumber}`;

            btn.appendChild(preview);
            btn.appendChild(label);
            btn.addEventListener('click', () => this.goToPage(pageNumber - 1));

            this.thumbsGridEl.appendChild(btn);
            this.thumbButtons.push(btn);

            if (this.renderedPages.has(pageNumber)) {
                this.applyThumbnailImage(pageNumber, this.renderedPages.get(pageNumber));
            }
        }
    }

    applyThumbnailImage(pageNumber, url) {
        const btn = this.thumbButtons[pageNumber - 1];
        if (!btn || !url) {
            return;
        }

        const img = btn.querySelector('img');
        const fallback = btn.querySelector('.sikera-thumb-fallback');
        if (img) {
            img.src = url;
            img.classList.remove('hidden');
        }
        if (fallback) {
            fallback.classList.add('hidden');
        }
    }

    toggleThumbnails() {
        if (!this.thumbsPanelEl || !this.thumbsToggleBtn) {
            return;
        }

        const isOpening = this.thumbsPanelEl.classList.contains('hidden');
        this.thumbsPanelEl.classList.toggle('hidden', !isOpening);
        this.thumbsToggleBtn.setAttribute('aria-expanded', isOpening ? 'true' : 'false');

        if (isOpening) {
            const limit = Math.min(this.totalPages, 24);
            for (let p = 1; p <= limit; p++) {
                this.ensurePageRendered(p);
            }
        }
    }

    goPrev() {
        if (!this.pageFlip || this.currentPage <= 0) {
            return;
        }

        if (this.reducedMotion) {
            this.pageFlip.turnToPrevPage();
        } else {
            this.pageFlip.flipPrev();
        }
    }

    goNext() {
        if (!this.pageFlip || this.currentPage >= this.totalPages - 1) {
            return;
        }

        if (this.reducedMotion) {
            this.pageFlip.turnToNextPage();
        } else {
            this.pageFlip.flipNext();
        }
    }

    goToPage(zeroBasedIndex, instant = false) {
        if (!this.pageFlip) {
            return;
        }

        const target = Math.max(0, Math.min(this.totalPages - 1, zeroBasedIndex));
        this.preloadAround(target);

        if (instant || this.reducedMotion) {
            this.pageFlip.turnToPage(target);
        } else {
            this.pageFlip.flip(target);
        }
    }

    setZoom(nextZoom) {
        this.zoom = Math.max(0.75, Math.min(2, Math.round(nextZoom * 100) / 100));
        this.applyZoom();
    }

    applyZoom() {
        if (this.zoomLayerEl) {
            this.zoomLayerEl.style.transform = `scale(${this.zoom})`;
            this.zoomLayerEl.style.transformOrigin = 'top center';
            const extraHeight = this.zoom > 1 ? Math.ceil(this.lastPageHeight * (this.zoom - 1)) : 0;
            this.zoomLayerEl.style.marginBottom = `${extraHeight}px`;
        }
        if (this.zoomLabelEl) {
            this.zoomLabelEl.textContent = `${Math.round(this.zoom * 100)}%`;
        }
        if (this.zoomOutBtn) {
            this.zoomOutBtn.disabled = this.zoom <= 0.75;
        }
        if (this.zoomInBtn) {
            this.zoomInBtn.disabled = this.zoom >= 2;
        }
    }

    setLayout(mode) {
        if (mode === 'spread' && this.totalPages <= 1) {
            return;
        }
        if (this.preferredLayout === mode) {
            return;
        }

        this.preferredLayout = mode;
        this.rebuildFlipbook(this.currentPage);
    }

    syncLayoutButtons(isSpreadActive) {
        if (this.layoutSingleBtn) {
            this.layoutSingleBtn.setAttribute('aria-pressed', (!isSpreadActive).toString());
            this.layoutSingleBtn.classList.toggle('bg-teal-700', !isSpreadActive);
            this.layoutSingleBtn.classList.toggle('text-white', !isSpreadActive);
            this.layoutSingleBtn.classList.toggle('dark:bg-teal-500', !isSpreadActive);
            this.layoutSingleBtn.classList.toggle('dark:text-slate-950', !isSpreadActive);
        }
        if (this.layoutSpreadBtn) {
            const canSpread = this.totalPages > 1 && (this.viewportEl?.clientWidth || 0) >= 700;
            this.layoutSpreadBtn.disabled = !canSpread;
            this.layoutSpreadBtn.setAttribute('aria-pressed', isSpreadActive.toString());
            this.layoutSpreadBtn.classList.toggle('bg-teal-700', isSpreadActive);
            this.layoutSpreadBtn.classList.toggle('text-white', isSpreadActive);
            this.layoutSpreadBtn.classList.toggle('dark:bg-teal-500', isSpreadActive);
            this.layoutSpreadBtn.classList.toggle('dark:text-slate-950', isSpreadActive);
        }
    }

    async toggleFullscreen() {
        if (!document.fullscreenElement) {
            try {
                await this.root.requestFullscreen();
            } catch {
                // Fullscreen API unavailable or blocked by browser context.
            }
        } else {
            await document.exitFullscreen();
        }
    }

    syncFullscreenButton() {
        const isFs = document.fullscreenElement === this.root;
        if (this.fullscreenBtn) {
            this.fullscreenBtn.setAttribute('aria-pressed', isFs ? 'true' : 'false');
            const label = this.fullscreenBtn.querySelector('[data-fullscreen-label]');
            if (label) {
                label.textContent = isFs ? 'Keluar Layar Penuh' : 'Layar Penuh';
            }
        }
        window.setTimeout(() => {
            if (this.pdfDoc && this.totalPages > 0) {
                this.rebuildFlipbook(this.currentPage);
            }
        }, 120);
    }

    syncControls() {
        const humanPage = this.currentPage + 1;
        const orientation = this.pageFlip ? this.pageFlip.getOrientation() : 'portrait';

        let rangeLabel = `Halaman ${humanPage} dari ${this.totalPages}`;
        if (orientation === 'landscape' && this.totalPages > 1 && humanPage > 1 && humanPage < this.totalPages) {
            rangeLabel = `Halaman ${humanPage} - ${Math.min(this.totalPages, humanPage + 1)} dari ${this.totalPages}`;
        }

        if (this.pageTotalEl) {
            this.pageTotalEl.textContent = String(this.totalPages);
        }
        if (this.pageRangeEl) {
            this.pageRangeEl.textContent = rangeLabel;
        }
        if (this.pageInput) {
            this.pageInput.max = String(this.totalPages);
            this.pageInput.value = String(humanPage);
        }
        if (this.scrubberEl) {
            this.scrubberEl.max = String(this.totalPages);
            this.scrubberEl.value = String(humanPage);
        }
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentPage <= 0;
        }
        if (this.nextBtn) {
            const isAtEnd = orientation === 'landscape' && this.totalPages > 1
                ? this.currentPage >= this.totalPages - 1 || (humanPage > 1 && humanPage + 1 >= this.totalPages)
                : this.currentPage >= this.totalPages - 1;
            this.nextBtn.disabled = isAtEnd;
        }
        if (this.announceEl) {
            this.announceEl.textContent = rangeLabel;
        }

        const isTwoPageSpread = orientation === 'landscape' && this.totalPages > 1 && humanPage > 1 && humanPage < this.totalPages;
        this.thumbButtons.forEach((btn, idx) => {
            const active = isTwoPageSpread
                ? (idx === this.currentPage || idx === this.currentPage + 1)
                : (idx === this.currentPage);
            btn.classList.toggle('border-teal-700', active);
            btn.classList.toggle('ring-2', active);
            btn.classList.toggle('ring-teal-700', active);
            btn.classList.toggle('dark:border-teal-400', active);
            btn.classList.toggle('dark:ring-teal-400', active);
            btn.setAttribute('aria-current', active ? 'page' : 'false');
        });
    }

    handleKeydown(event) {
        const tag = document.activeElement?.tagName?.toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') {
            return;
        }

        if (event.key === 'ArrowRight' || event.key === 'PageDown') {
            event.preventDefault();
            this.goNext();
        } else if (event.key === 'ArrowLeft' || event.key === 'PageUp') {
            event.preventDefault();
            this.goPrev();
        } else if (event.key === 'Home') {
            event.preventDefault();
            this.goToPage(0);
        } else if (event.key === 'End') {
            event.preventDefault();
            this.goToPage(this.totalPages - 1);
        }
    }

    cleanupUrls() {
        for (const url of this.renderedPages.values()) {
            if (typeof url === 'string' && url.startsWith('blob:')) {
                URL.revokeObjectURL(url);
            }
        }
        this.renderedPages.clear();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sikera-flipbook]').forEach((root) => {
        new SikeraPdfFlipbook(root);
    });
});
