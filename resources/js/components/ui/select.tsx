import { cn } from '@/utils';
import * as React from 'react';

const Select = React.forwardRef<HTMLSelectElement, React.ComponentProps<'select'>>(
    ({ className, children, ...props }, ref) => {
        return (
            <select
                ref={ref}
                data-slot="select"
                className={cn(
                    'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-9 text-sm font-normal text-slate-900 shadow-theme-xs transition-colors focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20 focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-teal-700/40 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500 aria-invalid:border-rose-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus:border-teal-400 dark:focus:ring-teal-400/20 dark:disabled:border-slate-700 dark:disabled:bg-slate-800/70 dark:disabled:text-slate-400',
                    className,
                )}
                {...props}
            >
                {children}
            </select>
        );
    },
);
Select.displayName = 'Select';

export { Select };
