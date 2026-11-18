<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SIKERA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body
    x-data="{ darkMode: false }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode')) === true;
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
    "
    :class="{ 'dark bg-slate-900': darkMode === true }"
    class="font-jakarta bg-slate-50 text-slate-900 antialiased"
>
    <div class="flex min-h-screen flex-col bg-slate-50 text-slate-900 transition-colors duration-150 dark:bg-slate-900 dark:text-slate-100">
        <header class="mx-auto flex w-full max-w-5xl items-center justify-between gap-4 px-4 pt-5 sm:px-6 lg:px-8">
            <a
                href="{{ route('login') }}"
                class="inline-flex min-h-11 items-center gap-3 rounded-lg px-1.5 py-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
            >
                <span class="flex h-8 w-1.5 shrink-0 flex-col overflow-hidden rounded-sm" aria-hidden="true">
                    <span class="h-5 w-full bg-teal-700 dark:bg-teal-400"></span>
                    <span class="h-3 w-full bg-rose-600 dark:bg-rose-500"></span>
                </span>
                <span class="flex flex-col">
                    <span class="text-base font-bold leading-tight tracking-tight text-slate-900 dark:text-white">SIKERA</span>
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Universitas Bengkulu</span>
                </span>
            </a>

            <button
                type="button"
                @click="darkMode = !darkMode"
                :aria-pressed="darkMode.toString()"
                :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
            >
                <span x-show="!darkMode" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 fill-current text-slate-700" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                    <span>Mode Gelap</span>
                </span>
                <span x-cloak x-show="darkMode" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 fill-current text-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zm0 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm9-8a1 1 0 110 2h-1a1 1 0 110-2h1zM5 11a1 1 0 110 2H4a1 1 0 110-2h1zm14.071-5.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.05 16.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm12.021.707a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.05 7.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.343 4.93l.707.707a1 1 0 010 1.414zM12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    <span>Mode Terang</span>
                </span>
            </button>
        </header>

        <main class="mx-auto flex w-full max-w-5xl flex-1 flex-col items-center justify-center px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            @yield('content')
        </main>

        <footer class="mx-auto w-full max-w-5xl px-4 pb-6 pt-2 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-2 border-t border-slate-200 pt-4 text-xs text-slate-600 dark:border-slate-800 dark:text-slate-300 sm:flex-row">
                <span>SIKERA &middot; Sistem Informasi Kesehatan Reproduksi Remaja</span>
                <span>Instrumen Edukasi &amp; Evaluasi Riset Universitas Bengkulu</span>
            </div>
        </footer>
    </div>
</body>
</html>
