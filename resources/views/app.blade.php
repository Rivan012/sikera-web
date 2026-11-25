<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/webp" href="{{ asset('images/sikera-logo.webp') }}">
    <title inertia>SIKERA | Sistem Informasi Kesehatan Reproduksi Remaja</title>
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('theme') || localStorage.getItem('theme-mode');
                var legacyDark = localStorage.getItem('darkMode');
                if (
                    saved === 'dark' ||
                    legacyDark === 'true' ||
                    (saved === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)
                ) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="font-jakarta bg-gray-50 text-gray-900 antialiased dark:bg-gray-900 dark:text-white/90">
    @inertia
</body>
</html>
