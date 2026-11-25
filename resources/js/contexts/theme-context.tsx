import { createContext, useContext, useEffect, useState, type ReactNode } from 'react';

export type ThemeMode = 'light' | 'dark' | 'auto';
export type ResolvedTheme = 'light' | 'dark';

type ThemeContextType = {
    theme: ResolvedTheme;
    themeMode: ThemeMode;
    setThemeMode: (mode: ThemeMode) => void;
    toggleTheme: () => void;
};

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

function detectInitialTheme(): ThemeMode {
    if (typeof window === 'undefined') {
        return 'light';
    }
    try {
        const savedMode = localStorage.getItem('theme-mode') as ThemeMode | null;
        if (savedMode === 'light' || savedMode === 'dark' || savedMode === 'auto') {
            return savedMode;
        }
        const savedTheme = localStorage.getItem('theme') as ResolvedTheme | null;
        if (savedTheme === 'light' || savedTheme === 'dark') {
            return savedTheme;
        }
        if (localStorage.getItem('darkMode') === 'true') {
            return 'dark';
        }
    } catch {
        // Ignore storage read error in restricted browsers.
    }
    return 'light';
}

export function ThemeProvider({ children }: { children: ReactNode }) {
    const [themeMode, setThemeModeState] = useState<ThemeMode>(detectInitialTheme);
    const [theme, setTheme] = useState<ResolvedTheme>(() => {
        const initial = detectInitialTheme();
        if (initial === 'auto' && typeof window !== 'undefined') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        return initial === 'dark' ? 'dark' : 'light';
    });

    useEffect(() => {
        try {
            localStorage.setItem('theme-mode', themeMode);
        } catch {
            // Ignore storage write error.
        }

        if (themeMode === 'auto') {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleChange = () => {
                setTheme(mediaQuery.matches ? 'dark' : 'light');
            };
            handleChange();
            mediaQuery.addEventListener('change', handleChange);
            return () => mediaQuery.removeEventListener('change', handleChange);
        }

        setTheme(themeMode);
        return undefined;
    }, [themeMode]);

    useEffect(() => {
        try {
            localStorage.setItem('theme', theme);
            localStorage.setItem('darkMode', JSON.stringify(theme === 'dark'));
        } catch {
            // Ignore storage write error.
        }

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-color-scheme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-color-scheme', 'light');
        }

        window.dispatchEvent(new CustomEvent('sikera-theme-change', { detail: { dark: theme === 'dark' } }));
    }, [theme]);

    const setThemeMode = (mode: ThemeMode) => {
        setThemeModeState(mode);
    };

    const toggleTheme = () => {
        setThemeModeState(theme === 'light' ? 'dark' : 'light');
    };

    return (
        <ThemeContext.Provider value={{ theme, themeMode, setThemeMode, toggleTheme }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (!context) {
        throw new Error('useTheme harus digunakan di dalam ThemeProvider');
    }
    return context;
}
