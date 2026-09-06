<header
    class="sticky top-0 z-99999 flex w-full border-gray-200 bg-white lg:border-b dark:border-gray-800 dark:bg-gray-900"
>
    <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
        <div class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">
            <button
                :class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : ''"
                class="z-99999 flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:h-11 lg:w-11 lg:border dark:border-gray-800 dark:text-gray-400"
                @click.stop="sidebarToggle = !sidebarToggle"
            >
                <svg class="fill-current" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z" fill=""/>
                </svg>
            </button>

            <div class="hidden lg:block">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-white">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2 2xsm:gap-3 lg:ml-auto">
                <button
                    @click="darkMode = !darkMode"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                >
                    <svg x-show="!darkMode" class="fill-current" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zm0 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm9-8a1 1 0 110 2h-1a1 1 0 110-2h1zM5 11a1 1 0 110 2H4a1 1 0 110-2h1zm14.071-5.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.05 16.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm12.021.707a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.05 7.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.343 4.93l.707.707a1 1 0 010 1.414zM12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    <svg x-show="darkMode" class="fill-current" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>

                <div class="hidden h-6 w-px bg-gray-200 dark:bg-gray-700 lg:block"></div>

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-500 font-semibold text-sm dark:bg-brand-500/10 dark:text-brand-400">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden lg:block">
                        <span class="block text-sm font-medium text-gray-800 dark:text-white">{{ auth()->user()->name }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
