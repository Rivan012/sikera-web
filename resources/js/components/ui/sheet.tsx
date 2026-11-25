import { Dialog as SheetPrimitive } from '@base-ui/react/dialog';
import * as React from 'react';
import { cn } from '@/utils';

function Sheet({ ...props }: SheetPrimitive.Root.Props) {
    return <SheetPrimitive.Root data-slot="sheet" {...props} />;
}

function SheetContent({
    className,
    children,
    side = 'left',
    ...props
}: SheetPrimitive.Popup.Props & { side?: 'left' | 'right' }) {
    return (
        <SheetPrimitive.Portal>
            <SheetPrimitive.Backdrop className="fixed inset-0 isolate z-100 bg-slate-950/55 duration-150 data-closed:animate-out data-closed:fade-out-0 data-open:animate-in data-open:fade-in-0 dark:bg-black/70" />
            <SheetPrimitive.Popup
                data-slot="sheet-content"
                data-side={side}
                className={cn(
                    'fixed inset-y-0 z-101 flex h-full w-[min(85vw,18.5rem)] flex-col border-slate-200 bg-white text-sm text-slate-700 shadow-theme-lg outline-none duration-150 data-closed:animate-out data-open:animate-in dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200',
                    'data-[side=left]:left-0 data-[side=left]:border-r data-[side=left]:data-closed:slide-out-to-left data-[side=left]:data-open:slide-in-from-left',
                    'data-[side=right]:right-0 data-[side=right]:border-l data-[side=right]:data-closed:slide-out-to-right data-[side=right]:data-open:slide-in-from-right',
                    className,
                )}
                {...props}
            >
                {children}
            </SheetPrimitive.Popup>
        </SheetPrimitive.Portal>
    );
}

function SheetTitle({ className, ...props }: SheetPrimitive.Title.Props) {
    return <SheetPrimitive.Title className={cn('text-base font-bold', className)} {...props} />;
}

function SheetDescription({ className, ...props }: SheetPrimitive.Description.Props) {
    return <SheetPrimitive.Description className={cn('text-sm', className)} {...props} />;
}

function SheetHeader({ className, ...props }: React.ComponentProps<'div'>) {
    return <div className={cn('flex flex-col gap-1', className)} {...props} />;
}

export { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle };
