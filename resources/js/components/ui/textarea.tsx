import { cn } from '@/utils';
import * as React from 'react';

const Textarea = React.forwardRef<HTMLTextAreaElement, React.ComponentProps<'textarea'>>(
    ({ className, ...props }, ref) => {
        return (
            <textarea
                ref={ref}
                data-slot="textarea"
                className={cn(
                    'flex min-h-24 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-900 shadow-theme-xs transition-colors placeholder:text-slate-500 focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20 focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-teal-700/40 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500 aria-invalid:border-rose-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-teal-400 dark:focus:ring-teal-400/20',
                    className,
                )}
                {...props}
            />
        );
    },
);
Textarea.displayName = 'Textarea';

export { Textarea };
