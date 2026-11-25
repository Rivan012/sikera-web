import { cn } from '@/utils';
import type { ReactNode } from 'react';

interface EmptyStateProps {
    title: string;
    description?: string;
    action?: ReactNode;
    icon?: ReactNode;
    className?: string;
}

export function EmptyState({ title, description, action, icon, className }: EmptyStateProps) {
    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center dark:border-slate-700 dark:bg-slate-800/60',
                className,
            )}
        >
            {icon && <div className="text-slate-500 dark:text-slate-400">{icon}</div>}
            <p className="text-sm font-semibold text-slate-900 dark:text-white">{title}</p>
            {description && <p className="max-w-md text-sm text-slate-600 dark:text-slate-300">{description}</p>}
            {action}
        </div>
    );
}
