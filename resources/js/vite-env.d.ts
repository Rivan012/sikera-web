/// <reference types="vite/client" />

declare module 'page-flip/dist/js/page-flip.module.js' {
    export interface PageFlipSettings {
        width: number;
        height: number;
        size?: 'fixed' | 'stretch';
        minWidth?: number;
        maxWidth?: number;
        minHeight?: number;
        maxHeight?: number;
        startPage?: number;
        drawShadow?: boolean;
        maxShadowOpacity?: number;
        flippingTime?: number;
        usePortrait?: boolean;
        showCover?: boolean;
        autoSize?: boolean;
        mobileScrollSupport?: boolean;
        clickEventForward?: boolean;
        useMouseEvents?: boolean;
        showPageCorners?: boolean;
        disableFlipByClick?: boolean;
        swipeDistance?: number;
    }

    export interface PageFlipEvent {
        data: unknown;
    }

    export class PageFlip {
        constructor(element: HTMLElement, settings: PageFlipSettings);
        loadFromHTML(items: NodeListOf<HTMLElement> | HTMLElement[]): void;
        on(event: 'flip' | 'changeOrientation' | 'changeState' | 'init', callback: (e: PageFlipEvent) => void): PageFlip;
        flip(page: number): void;
        flipNext(): void;
        flipPrev(): void;
        turnToPage(page: number): void;
        turnToNextPage(): void;
        turnToPrevPage(): void;
        getOrientation(): 'portrait' | 'landscape';
        getCurrentPageIndex(): number;
        destroy(): void;
    }
}

declare module 'pdfjs-dist/build/pdf.worker.min.mjs?url' {
    const url: string;
    export default url;
}
