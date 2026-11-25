import { cn } from '@/utils';

interface ProgressBarProps {
    value: number;
    label: string;
    className?: string;
    tone?: 'teal' | 'pine' | 'amber' | 'rose';
}

const TONE: Record<NonNullable<ProgressBarProps['tone']>, string> = {
    teal: 'bg-teal-700 dark:bg-teal-400',
    pine: 'bg-pine-700 dark:bg-pine-400',
    amber: 'bg-amber-700 dark:bg-amber-300',
    rose: 'bg-rose-700 dark:bg-rose-400',
};

export function ProgressBar({ value, label, className, tone = 'teal' }: ProgressBarProps) {
    const safe = Math.max(0, Math.min(100, Math.round(value)));

    return (
        <div
            role="progressbar"
            aria-label={label}
            aria-valuemin={0}
            aria-valuemax={100}
            aria-valuenow={safe}
            className={cn('h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700', className)}
        >
            <div className={cn('h-full rounded-full transition-[width] duration-300', TONE[tone])} style={{ width: `${safe}%` }} />
        </div>
    );
}
