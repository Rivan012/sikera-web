import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { FlashMessages, ThemeToggleButton } from '@/layouts/AppLayout';
import { cn } from '@/utils';

interface AuthLayoutProps {
    children: ReactNode;
    width?: 'sm' | 'md' | 'lg' | 'xl';
}

const WIDTH: Record<NonNullable<AuthLayoutProps['width']>, string> = {
    sm: 'max-w-md',
    md: 'max-w-2xl',
    lg: 'max-w-4xl',
    xl: 'max-w-6xl',
};

export default function AuthLayout({ children, width = 'lg' }: AuthLayoutProps) {
    return (
        <div className="flex min-h-svh flex-col bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100">
            <header className="mx-auto flex w-full max-w-6xl items-center justify-between gap-2 px-4 pt-5 sm:gap-4 sm:px-6 lg:px-8">
                <Link
                    href="/"
                    aria-label="Kembali ke beranda SIKERA"
                    className="inline-flex min-h-11 items-center gap-2.5 rounded-lg px-1.5 py-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 sm:gap-3 dark:focus-visible:outline-teal-400"
                >
                    <img src="/images/sikera-logo.webp" alt="" width={44} height={44} className="shrink-0" />
                    <span className="flex flex-col">
                        <span className="text-base leading-tight font-bold tracking-tight text-slate-900 dark:text-white">SIKERA</span>
                        <span className="hidden text-xs font-medium text-slate-600 sm:block dark:text-slate-300">
                            Sistem Informasi Kesehatan Remaja
                        </span>
                    </span>
                </Link>
                <div className="flex shrink-0 items-center gap-2">
                    <Link
                        href="/"
                        className="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                    >
                        Beranda
                    </Link>
                    <ThemeToggleButton />
                </div>
            </header>

            <main className={cn('mx-auto flex w-full flex-1 flex-col justify-center px-4 py-6 sm:px-6 sm:py-8 lg:px-8', WIDTH[width])}>
                <FlashMessages />
                {children}
            </main>

            <footer className="mx-auto w-full max-w-6xl px-4 pt-2 pb-6 sm:px-6 lg:px-8">
                <div className="flex flex-col items-center justify-between gap-2 border-t border-slate-200 pt-4 text-xs text-slate-600 sm:flex-row dark:border-slate-800 dark:text-slate-300">
                    <span>SIKERA (Sistem Informasi Kesehatan Remaja)</span>
                    <span>Edukasi dan evaluasi riset di tiga universitas</span>
                </div>
            </footer>
        </div>
    );
}
