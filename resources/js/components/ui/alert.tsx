import { cn } from '@/utils';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

const alertVariants = cva(
    'relative grid w-full gap-1 rounded-xl border px-4 py-3 text-start text-sm',
    {
        variants: {
            variant: {
                default:
                    'border-slate-300 bg-white text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100',
                success:
                    'border-teal-300 bg-teal-50 text-teal-950 dark:border-teal-700 dark:bg-teal-950/80 dark:text-teal-100',
                warning:
                    'border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-700 dark:bg-amber-950/80 dark:text-amber-100',
                destructive:
                    'border-rose-300 bg-rose-50 text-rose-950 dark:border-rose-700 dark:bg-rose-950/80 dark:text-rose-100',
                info:
                    'border-sky-300 bg-sky-50 text-sky-950 dark:border-sky-700 dark:bg-sky-950/80 dark:text-sky-100',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
);

function Alert({
    className,
    variant,
    ...props
}: React.ComponentProps<'div'> & VariantProps<typeof alertVariants>) {
    return (
        <div
            data-slot="alert"
            role={variant === 'destructive' || variant === 'warning' ? 'alert' : 'status'}
            className={cn(alertVariants({ variant }), className)}
            {...props}
        />
    );
}

function AlertTitle({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="alert-title"
            className={cn('text-sm font-bold leading-snug', className)}
            {...props}
        />
    );
}

function AlertDescription({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="alert-description"
            className={cn('text-sm leading-relaxed', className)}
            {...props}
        />
    );
}

export { Alert, AlertDescription, AlertTitle };
