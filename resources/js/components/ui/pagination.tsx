import { Link } from '@inertiajs/react';
import { cn } from '@/utils';
import type { PaginationLink } from '@/types/sikera';

interface PaginationProps {
    links: PaginationLink[];
    className?: string;
}

function cleanLabel(label: string): string {
    return label
        .replace('&laquo;', '')
        .replace('&raquo;', '')
        .replace('Previous', 'Sebelumnya')
        .replace('Next', 'Berikutnya')
        .replace('pagination.previous', 'Sebelumnya')
        .replace('pagination.next', 'Berikutnya')
        .trim();
}

export function Pagination({ links, className }: PaginationProps) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <nav aria-label="Navigasi halaman" className={cn('flex flex-wrap items-center gap-1.5', className)}>
            {links.map((link, index) => {
                const label = cleanLabel(link.label);
                const base =
                    'inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border px-3 text-sm font-semibold';

                if (!link.url) {
                    return (
                        <span
                            key={`${label}-${index}`}
                            aria-disabled="true"
                            className={cn(base, 'border-slate-200 text-slate-400 dark:border-slate-700 dark:text-slate-500')}
                        >
                            {label}
                        </span>
                    );
                }

                return (
                    <Link
                        key={`${label}-${index}`}
                        href={link.url}
                        preserveScroll
                        aria-current={link.active ? 'page' : undefined}
                        className={cn(
                            base,
                            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400',
                            link.active
                                ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                : 'border-slate-300 bg-white text-slate-800 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700',
                        )}
                    >
                        {label}
                    </Link>
                );
            })}
        </nav>
    );
}
