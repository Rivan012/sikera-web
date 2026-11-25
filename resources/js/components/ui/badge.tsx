import { cn } from '@/utils';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

const badgeVariants = cva(
    'inline-flex w-fit shrink-0 items-center justify-center gap-1 rounded-lg border font-semibold whitespace-nowrap select-none [&>svg]:pointer-events-none [&>svg]:shrink-0',
    {
        variants: {
            variant: {
                light: '',
                solid: '',
            },
            color: {
                primary: '',
                success: '',
                error: '',
                warning: '',
                info: '',
                gray: '',
            },
            size: {
                sm: 'px-2 py-0.5 text-xs [&>svg]:size-3',
                default: 'px-2.5 py-1 text-xs [&>svg]:size-3.5',
                lg: 'px-3 py-1 text-sm [&>svg]:size-4',
            },
        },
        compoundVariants: [
            {
                variant: 'light',
                color: 'primary',
                className:
                    'border-teal-200 bg-teal-50 text-teal-900 dark:border-teal-800 dark:bg-teal-950/80 dark:text-teal-200',
            },
            {
                variant: 'solid',
                color: 'primary',
                className:
                    'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950',
            },
            {
                variant: 'light',
                color: 'success',
                className:
                    'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-200',
            },
            {
                variant: 'solid',
                color: 'success',
                className:
                    'border-emerald-700 bg-emerald-700 text-white dark:border-emerald-400 dark:bg-emerald-400 dark:text-slate-950',
            },
            {
                variant: 'light',
                color: 'error',
                className:
                    'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-800 dark:bg-rose-950/80 dark:text-rose-200',
            },
            {
                variant: 'solid',
                color: 'error',
                className:
                    'border-rose-700 bg-rose-700 text-white dark:border-rose-400 dark:bg-rose-400 dark:text-slate-950',
            },
            {
                variant: 'light',
                color: 'warning',
                className:
                    'border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-700 dark:bg-amber-950/80 dark:text-amber-200',
            },
            {
                variant: 'solid',
                color: 'warning',
                className:
                    'border-amber-800 bg-amber-800 text-white dark:border-amber-300 dark:bg-amber-300 dark:text-slate-950',
            },
            {
                variant: 'light',
                color: 'info',
                className:
                    'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-800 dark:bg-sky-950/80 dark:text-sky-200',
            },
            {
                variant: 'solid',
                color: 'info',
                className:
                    'border-sky-700 bg-sky-700 text-white dark:border-sky-300 dark:bg-sky-300 dark:text-slate-950',
            },
            {
                variant: 'light',
                color: 'gray',
                className:
                    'border-slate-200 bg-slate-100 text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
            },
            {
                variant: 'solid',
                color: 'gray',
                className:
                    'border-slate-700 bg-slate-700 text-white dark:border-slate-600 dark:bg-slate-700 dark:text-white',
            },
        ],
        defaultVariants: {
            variant: 'light',
            color: 'primary',
            size: 'default',
        },
    },
);

export type BadgeProps = Omit<React.ComponentProps<'span'>, 'color'> &
    VariantProps<typeof badgeVariants>;

function Badge({
    className,
    variant = 'light',
    color = 'primary',
    size = 'default',
    ...props
}: BadgeProps) {
    return (
        <span
            data-slot="badge"
            className={cn(badgeVariants({ variant, color, size }), className)}
            {...props}
        />
    );
}

export { Badge, badgeVariants };
