import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import '../css/app.css';
import { ThemeProvider } from '@/contexts/theme-context';

const pages = import.meta.glob<{ default: ComponentType }>('./Pages/**/*.tsx');

createInertiaApp({
    title: (title) => (title ? `${title} | SIKERA` : 'SIKERA | Sistem Informasi Kesehatan Reproduksi Remaja'),
    resolve: async (name) => {
        const loader = pages[`./Pages/${name}.tsx`];
        if (!loader) {
            throw new Error(`Halaman Inertia tidak ditemukan: ${name}`);
        }
        return (await loader()).default;
    },
    withApp: (app) => <ThemeProvider>{app}</ThemeProvider>,
    progress: {
        color: '#0f766e',
        showSpinner: false,
    },
});
