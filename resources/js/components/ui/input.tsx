import { cn } from '@/utils';
import * as React from 'react';

const Input = React.forwardRef<HTMLInputElement, React.ComponentProps<'input'>>(
    ({ className, type = 'text', ...props }, ref) => {
        return (
            <input
                ref={ref}
                type={type}
                data-slot="input"
                className={cn(
                    'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-900 shadow-theme-xs transition-colors placeholder:text-slate-500 focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20 focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-teal-700/40 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500 aria-invalid:border-rose-600 aria-invalid:ring-2 aria-invalid:ring-rose-600/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:ring-teal-400/20 dark:disabled:border-slate-700 dark:disabled:bg-slate-800/70 dark:disabled:text-slate-400 dark:aria-invalid:border-rose-400',
                    className,
                )}
                {...props}
            />
        );
    },
);
Input.displayName = 'Input';

export { Input };
