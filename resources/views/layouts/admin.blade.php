<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') | SIKERA (Sistem Informasi Kesehatan Remaja)</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/sikera-logo.webp') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('styles')
</head>
<body
    x-data="{
        page: '{{ $page ?? 'dashboard' }}',
        loaded: true,
        darkMode: false,
        stickyMenu: false,
        sidebarToggle: false,
        scrollTop: false
    }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode')) === true;
        $watch('darkMode', value => {
            localStorage.setItem('darkMode', JSON.stringify(value));
            window.dispatchEvent(new CustomEvent('sikera-theme-change', { detail: { dark: value } }));
        });
    "
    @keydown.escape.window="sidebarToggle = false"
    :class="{ 'dark bg-slate-900': darkMode === true }"
    class="font-jakarta bg-slate-50 text-slate-900 antialiased dark:text-slate-100"
>
    <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-900">
        @include('layouts.partials.sidebar')

        <div class="relative flex flex-1 flex-col overflow-x-hidden overflow-y-auto">
            <div
                x-cloak
                x-show="sidebarToggle"
                @click="sidebarToggle = false"
                class="fixed inset-0 z-9998 bg-slate-950/50 lg:hidden"
                aria-hidden="true"
            ></div>

            @include('layouts.partials.header')

            <main class="flex-1">
                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 sm:p-6 lg:p-8">
                    @if(session('success'))
                    <div
                        role="status"
                        x-data="{ show: true }"
                        x-show="show"
                        class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-teal-300 bg-teal-50 px-4 py-2.5 text-sm font-medium text-teal-950 dark:border-teal-700 dark:bg-teal-950/80 dark:text-teal-100"
                    >
                        <span>{{ session('success') }}</span>
                        <button
                            type="button"
                            @click="show = false"
                            aria-label="Tutup pemberitahuan berhasil"
                            class="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg text-teal-800 hover:bg-teal-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-200 dark:hover:bg-teal-900"
                        >
                            &times;
                        </button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div
                        role="status"
                        x-data="{ show: true }"
                        x-show="show"
                        class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                    >
                        <span>{{ session('info') }}</span>
                        <button
                            type="button"
                            @click="show = false"
                            aria-label="Tutup informasi"
                            class="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            &times;
                        </button>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div
                        role="alert"
                        x-data="{ show: true }"
                        x-show="show"
                        class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-950 dark:border-amber-700 dark:bg-amber-950/80 dark:text-amber-100"
                    >
                        <span>{{ session('warning') }}</span>
                        <button
                            type="button"
                            @click="show = false"
                            aria-label="Tutup peringatan"
                            class="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg text-amber-900 hover:bg-amber-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 dark:text-amber-200 dark:hover:bg-amber-900"
                        >
                            &times;
                        </button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div role="alert" class="mb-5 rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm font-medium text-rose-950 dark:border-rose-700 dark:bg-rose-950/80 dark:text-rose-100">
                        <ul class="flex list-disc flex-col gap-1 pl-5">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
