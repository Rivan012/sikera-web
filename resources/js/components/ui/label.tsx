import { cn } from '@/utils';
import * as React from 'react';

function Label({ className, ...props }: React.ComponentProps<'label'>) {
    return (
        <label
            data-slot="label"
            className={cn(
                'flex items-center gap-1.5 text-xs font-semibold text-slate-700 select-none dark:text-slate-200',
                className,
            )}
            {...props}
        />
    );
}

export { Label };
