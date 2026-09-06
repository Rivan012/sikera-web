<aside
    :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
    <div
        :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 sidebar-header pb-7"
    >
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white font-bold text-lg shadow-theme-xs">S</span>
            <span class="logo font-bold text-lg text-gray-900 dark:text-white" :class="sidebarToggle ? 'hidden' : ''">SIKERA</span>
        </a>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav x-data="{selected: $persist('Dashboard')}">
            {{-- 1. MENU UTAMA SESUAI PERAN --}}
            <div>
                <h3 class="mb-3 text-xs uppercase font-bold tracking-wider text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        {{ auth()->user()->isAdmin() ? 'PENGELOLA RISET' : 'MENU UTAMA' }}
                    </span>
                </h3>

                <ul class="flex flex-col gap-1.5 mb-5">
                    @if(auth()->user()->isMahasiswa())
                    <li>
                        <a
                            href="{{ route('mahasiswa.dashboard') }}"
                            class="menu-item group {{ request()->routeIs('mahasiswa.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('mahasiswa.dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Menu Utama</span>
                        </a>
                    </li>
                    @elseif(auth()->user()->isDosen())
                    <li>
                        <a
                            href="{{ route('dosen.dashboard') }}"
                            class="menu-item group {{ request()->routeIs('dosen.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('dosen.dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard Dosen PA</span>
                        </a>
                    </li>
                    @else
                    {{-- Super Admin Menu --}}
                    <li>
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="menu-item group {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('admin.dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h1.5v3H12V5zm-2 0h1.5v3H10V5zm-2 0h1.5v3H8V5zm-2 0h1.5v3H6V5zm12 14H6v-9h12v9z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard Grafik</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="menu-item group {{ request()->routeIs('admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('admin.users.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Daftar Pengguna</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('admin.modules.index') }}"
                            class="menu-item group {{ request()->routeIs('admin.modules.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('admin.modules.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12zM10 9h8v2h-8V9zm0 3h4v2h-4v-2zm0-6h8v2h-8V6z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Kelola Modul</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('admin.sheets.index') }}"
                            class="menu-item group {{ request()->routeIs('admin.sheets.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('admin.sheets.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Lembar Data</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('admin.posters.index') }}"
                            class="menu-item group {{ request()->routeIs('admin.posters.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        >
                            <svg class="{{ request()->routeIs('admin.posters.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Unggah Poster</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- 2. KELOMPOK FITUR EDUKASI & KONTEN (TAMPIL UNTUK SEMUA ROLE) --}}
            <div>
                <h3 class="mb-3 text-xs uppercase font-bold tracking-wider text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">EDUKASI &amp; RISET</span>
                </h3>

                <ul class="flex flex-col gap-1.5 mb-5">
                    {{-- I1: Modul Edukasi & Video YouTube --}}
                    <li>
                        <a href="{{ route('modules.index') }}" class="menu-item group {{ request()->routeIs('modules.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('modules.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12zM10 9h8v2h-8V9zm0 3h4v2h-4v-2zm0-6h8v2h-8V6z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Modul Edukasi</span>
                        </a>
                    </li>

                    {{-- I2: Projek Visual Poster (Kaspro) --}}
                    <li>
                        <a href="{{ route('posters.index') }}" class="menu-item group {{ request()->routeIs('posters.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('posters.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Visual Poster Kaspro</span>
                        </a>
                    </li>

                    {{-- I3: Kalender Haid & IMT (Mahasiswa) --}}
                    @if(auth()->user()->isMahasiswa())
                    <li>
                        <a href="{{ route('selfcare.index') }}" class="menu-item group {{ request()->routeIs('selfcare.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('selfcare.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                @if(auth()->user()->gender === 'P')
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                @else
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 2h1.5v3H12V5zm-2 0h1.5v3H10V5zm-2 0h1.5v3H8V5zm-2 0h1.5v3H6V5zm12 14H6v-9h12v9z"/>
                                @endif
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                {{ auth()->user()->gender === 'P' ? 'Kalender Haid & IMT' : 'Kalkulator IMT & Gizi' }}
                            </span>
                        </a>
                    </li>
                    @endif

                    {{-- I4: Diskusi Anonim & Kasus Kampus --}}
                    <li>
                        <a href="{{ route('cases.index') }}" class="menu-item group {{ request()->routeIs('cases.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('cases.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Studi Kasus Kampus</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('forum.index') }}" class="menu-item group {{ request()->routeIs('forum.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('forum.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/><path d="M12 10H7v2h5v-2zm5-4H7v2h10V6z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Forum Konsultasi</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- 3. GAMIFIKASI (UNTUK MAHASISWA) --}}
            @if(auth()->user()->isMahasiswa())
            <div>
                <h3 class="mb-3 text-xs uppercase font-bold tracking-wider text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">GAMIFIKASI</span>
                </h3>
                <ul class="flex flex-col gap-1.5 mb-5">
                    <li>
                        <a href="{{ route('gamification.trivia') }}" class="menu-item group {{ request()->routeIs('gamification.trivia*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('gamification.trivia*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Trivia Harian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gamification.myth_fact') }}" class="menu-item group {{ request()->routeIs('gamification.myth_fact*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('gamification.myth_fact*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Mitos vs Fakta</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gamification.diary') }}" class="menu-item group {{ request()->routeIs('gamification.diary*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="{{ request()->routeIs('gamification.diary*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Catatan Mood</span>
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            <div class="mt-auto pt-6 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3 px-3 pb-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-500 font-semibold dark:bg-brand-500/10 dark:text-brand-400">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0" :class="sidebarToggle ? 'lg:hidden' : ''">
                        <p class="text-xs font-semibold text-gray-800 truncate dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            {{ auth()->user()->role === 'dosen_pa' ? 'Dosen PA' : (auth()->user()->role === 'admin' ? 'Super Admin' : 'Mahasiswa') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" :class="sidebarToggle ? 'lg:hidden' : ''">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-error-500 dark:text-gray-500 dark:hover:text-error-400 transition" title="Pilih Keluar / Logout">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" fill="currentColor"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</aside>
