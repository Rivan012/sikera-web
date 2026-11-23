@php
    $currentUser = auth()->user();
    $roleLabel = match ($currentUser->role) {
        'dosen_pa' => 'Dosen Pembimbing Akademik',
        'admin' => 'Pengelola Riset',
        default => 'Mahasiswa Responden',
    };
    $avatarInitial = mb_strtoupper(mb_substr($currentUser->initials ?: $currentUser->name, 0, 2));
@endphp

<header class="sticky top-0 z-9999 border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <div class="flex w-full items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                @click.stop="sidebarToggle = !sidebarToggle"
                :aria-expanded="sidebarToggle.toString()"
                aria-label="Buka atau tutup menu navigasi samping"
                :class="sidebarToggle ? 'bg-slate-100 dark:bg-slate-800 lg:bg-white dark:lg:bg-slate-900' : 'bg-white dark:bg-slate-900'"
                class="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16" />
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-sm font-bold tracking-tight text-slate-900 dark:text-white sm:text-base lg:text-lg">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            <button
                type="button"
                @click="darkMode = !darkMode"
                :aria-pressed="darkMode.toString()"
                :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
            >
                <span x-show="!darkMode" class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0 fill-current text-slate-700" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                    <span class="hidden sm:inline">Mode Gelap</span>
                </span>
                <span x-cloak x-show="darkMode" class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0 fill-current text-amber-400" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zm0 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm9-8a1 1 0 110 2h-1a1 1 0 110-2h1zM5 11a1 1 0 110 2H4a1 1 0 110-2h1zm14.071-5.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.05 16.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm12.021.707a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.05 7.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.343 4.93l.707.707a1 1 0 010 1.414zM12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    <span class="hidden sm:inline">Mode Terang</span>
                </span>
            </button>

            <div class="hidden h-6 w-px bg-slate-200 dark:bg-slate-700 md:block" aria-hidden="true"></div>

            <a href="{{ route('profile.edit') }}" aria-label="Edit profil" title="Edit profil"
                class="flex min-h-11 min-w-11 items-center justify-center gap-2.5 rounded-lg px-1 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-teal-200 bg-teal-50 text-xs font-bold text-teal-800 dark:border-teal-800 dark:bg-teal-950 dark:text-teal-200">
                    @if ($currentUser->avatar)
                        <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" width="36" height="36"
                            referrerpolicy="no-referrer" class="h-full w-full object-cover">
                    @else
                        <span>{{ $avatarInitial }}</span>
                    @endif
                </div>
                <div class="hidden md:block">
                    <span class="block max-w-[160px] truncate text-xs font-semibold text-slate-900 dark:text-white">{{ $currentUser->name }}</span>
                    <span class="block text-[11px] font-medium text-slate-600 dark:text-slate-300">{{ $roleLabel }}</span>
                </div>
            </a>
        </div>
    </div>
</header>
