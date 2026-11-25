import { cn } from '@/utils';
import * as React from 'react';

function Skeleton({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="skeleton"
            aria-hidden="true"
            className={cn('animate-pulse rounded-md bg-slate-200 dark:bg-slate-700', className)}
            {...props}
        />
    );
}

export { Skeleton };
