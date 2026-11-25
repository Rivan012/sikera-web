import { cn } from '@/utils';
import * as React from 'react';

function TableWrapper({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="table-wrapper"
            className={cn(
                'w-full max-w-full overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/90',
                className,
            )}
            {...props}
        />
    );
}

function Table({ className, ...props }: React.ComponentProps<'table'>) {
    return <table className={cn('w-full min-w-full text-left text-sm', className)} {...props} />;
}

function TableHeader({ className, ...props }: React.ComponentProps<'thead'>) {
    return (
        <thead
            className={cn(
                'border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200',
                className,
            )}
            {...props}
        />
    );
}

function TableBody({ className, ...props }: React.ComponentProps<'tbody'>) {
    return <tbody className={cn('divide-y divide-slate-200 dark:divide-slate-700', className)} {...props} />;
}

function TableRow({ className, ...props }: React.ComponentProps<'tr'>) {
    return <tr className={cn('align-top', className)} {...props} />;
}

function TableHead({ className, ...props }: React.ComponentProps<'th'>) {
    return <th scope="col" className={cn('px-4 py-3 whitespace-nowrap', className)} {...props} />;
}

function TableCell({ className, ...props }: React.ComponentProps<'td'>) {
    return <td className={cn('px-4 py-3 text-slate-800 dark:text-slate-100', className)} {...props} />;
}

export { Table, TableBody, TableCell, TableHead, TableHeader, TableRow, TableWrapper };
