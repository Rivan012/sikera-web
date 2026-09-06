<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') | SIKERA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('styles')
</head>
<body
    x-data="{ page: '{{ $page ?? 'dashboard' }}', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false, scrollTop: false }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode'));
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
>
    <div class="flex h-screen overflow-hidden">
        @include('layouts.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <div
                @click="sidebarToggle = false"
                class="fixed inset-0 z-9998 bg-gray-900/50 hidden"
                :class="sidebarToggle ? 'lg:hidden block' : 'hidden'"
            ></div>

            @include('layouts.partials.header')

            <main>
                <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                    @if(session('success'))
                    <div class="mb-4 rounded-xl border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300" x-data="{show: true}" x-show="show" x-transition>
                        <div class="flex items-center justify-between">
                            <span>{{ session('success') }}</span>
                            <button @click="show = false" class="ml-4 text-success-500 hover:text-success-700">&times;</button>
                        </div>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="mb-4 rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-300" x-data="{show: true}" x-show="show" x-transition>
                        <div class="flex items-center justify-between">
                            <span>{{ session('warning') }}</span>
                            <button @click="show = false" class="ml-4 text-warning-500 hover:text-warning-700">&times;</button>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-4 rounded-xl border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
                        <ul class="list-disc pl-4">
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
