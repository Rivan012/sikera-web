import { cn } from '@/utils';
import * as React from 'react';

function Card({
    className,
    size = 'default',
    ...props
}: React.ComponentProps<'div'> & { size?: 'default' | 'sm' }) {
    return (
        <div
            data-slot="card"
            data-size={size}
            className={cn(
                'group/card flex flex-col gap-5 rounded-xl border border-slate-200 bg-white p-5 data-[size=sm]:p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/90',
                className,
            )}
            {...props}
        />
    );
}

function CardHeader({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-header"
            className={cn('flex flex-col gap-1.5 group-data-[size=sm]/card:gap-1', className)}
            {...props}
        />
    );
}

function CardTitle({ className, ...props }: React.ComponentProps<'h2'>) {
    return (
        <h2
            data-slot="card-title"
            className={cn(
                'text-lg font-bold tracking-tight text-slate-900 dark:text-white',
                className,
            )}
            {...props}
        />
    );
}

function CardDescription({ className, ...props }: React.ComponentProps<'p'>) {
    return (
        <p
            data-slot="card-description"
            className={cn('text-sm leading-relaxed text-slate-600 dark:text-slate-300', className)}
            {...props}
        />
    );
}

function CardContent({ className, ...props }: React.ComponentProps<'div'>) {
    return <div data-slot="card-content" className={cn('', className)} {...props} />;
}

function CardFooter({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-footer"
            className={cn(
                'flex items-center border-t border-slate-200 pt-4 dark:border-slate-700',
                className,
            )}
            {...props}
        />
    );
}

export { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle };
