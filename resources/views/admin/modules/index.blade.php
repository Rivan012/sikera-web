@extends('layouts.admin')

@section('title', 'Kelola & Unggah Modul Edukasi')
@section('page-title', 'Kelola Modul & Subbab PDF Flipbook')

@section('content')
<div
    x-data="{
        addingTopicTo: null,
        createPdfName: '',
        createPdfSize: '',
        createBannerMode: 'auto',
        createBannerTheme: 'teal',
        createBannerPreviewUrl: '',
        createUploadedFileName: '',
        submittingCreate: false,

        topicPdfNames: {},
        submittingTopicId: null,

        editTopicModalOpen: false,
        editTopic: { id: null, moduleNumber: '', code: '', title: '', youtube: '', currentPdfName: '', currentPdfSize: '' },
        editTopicNewPdfName: '',
        editTopicNewPdfSize: '',
        submittingEditTopic: false,

        deleteModalOpen: false,
        moduleToDelete: { id: null, number: '', title: '' },

        deleteTopicModalOpen: false,
        topicToDelete: { id: null, code: '', title: '' },

        uploadExcelModalOpen: false,
        selectedExcelFileName: '',
        isUploadingExcel: false,

        bannerModalOpen: false,
        bannerModule: { id: null, number: '', title: '', subtitle: '', currentBanner: '' },
        editBannerMode: 'auto',
        editBannerTheme: 'teal',
        editBannerPreviewUrl: '',
        editUploadedFileName: '',

        formatFileSize(bytes) {
            if (!bytes || bytes <= 0) return '';
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(2).replace('.', ',') + ' MB';
            return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        },
        openEditTopicModal(topic) {
            this.editTopic = { ...topic };
            this.editTopicNewPdfName = '';
            this.editTopicNewPdfSize = '';
            this.submittingEditTopic = false;
            this.editTopicModalOpen = true;
        },
        openDeleteTopicModal(topic) {
            this.topicToDelete = { ...topic };
            this.deleteTopicModalOpen = true;
        },
        closeAllModals() {
            this.deleteModalOpen = false;
            this.deleteTopicModalOpen = false;
            this.editTopicModalOpen = false;
            this.uploadExcelModalOpen = false;
            this.bannerModalOpen = false;
        }
    }"
    @keydown.escape.window="closeAllModals()"
    class="flex flex-col gap-6"
>
    {{-- Header Manajemen Modul dengan Editorial Index Bar --}}
    <section aria-labelledby="admin-modules-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex h-1.5 w-full" aria-hidden="true">
            <span class="w-20 bg-rose-600 dark:bg-rose-500"></span>
            <span class="grow bg-teal-700 dark:bg-teal-500"></span>
        </div>

        <div class="flex flex-col justify-between gap-4 p-5 sm:p-6 lg:flex-row lg:items-center">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                    Manajemen Kurikulum &middot; {{ $modules->count() }} Modul &middot; {{ $pdfTopicsCount }}/{{ $totalTopics }} Subbab Berformat PDF Flipbook
                </p>
                <h2 id="admin-modules-heading" class="mt-1 text-xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                    Kelola Modul &amp; Unggah PDF Subbab
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300 sm:text-sm">
                    Modul berfungsi sebagai wadah kurikulum (judul, deskripsi, dan banner). Setiap subbab diunggah dalam format berkas PDF dan otomatis dibaca mahasiswa sebagai flipbook interaktif.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.modules.template') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    Template Excel
                </a>
                <button
                    type="button"
                    @click="uploadExcelModalOpen = true"
                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-teal-700 bg-teal-50 px-3.5 py-2 text-xs font-semibold text-teal-800 transition-colors hover:bg-teal-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-teal-500 dark:bg-teal-950/70 dark:text-teal-200 dark:hover:bg-teal-900 dark:focus-visible:outline-teal-400"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Impor Excel
                </button>
                <a
                    href="{{ route('modules.index') }}"
                    class="inline-flex min-h-11 items-center gap-1.5 rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                >
                    Pratinjau Katalog
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-12">
        {{-- Kolom Kiri (5 kolom): Formulir Buat Modul Baru + Unggah PDF Subbab Pertama --}}
        <section aria-labelledby="create-module-heading" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6 xl:col-span-5">
            <div class="border-b border-slate-200 pb-4 dark:border-slate-700">
                <h3 id="create-module-heading" class="text-base font-bold text-slate-900 dark:text-white">
                    Unggah Modul Edukasi Baru
                </h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    Isi informasi wadah modul dan unggah berkas PDF untuk subbab pertamanya. Anda dapat menambahkan subbab PDF berikutnya setelah modul tersimpan.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.modules.store') }}"
                enctype="multipart/form-data"
                @submit="submittingCreate = true"
                class="mt-5 flex flex-col gap-4"
            >
                @csrf

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="create-module-number" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Nomor Modul <span class="text-rose-700 dark:text-rose-300">*</span>
                        </label>
                        <input
                            id="create-module-number"
                            type="number"
                            min="1"
                            name="module_number"
                            value="{{ old('module_number', $modules->max('module_number') + 1) }}"
                            required
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus-visible:outline-teal-400"
                        >
                    </div>
                    <div>
                        <label for="create-estimated-time" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Estimasi Waktu <span class="text-rose-700 dark:text-rose-300">*</span>
                        </label>
                        <input
                            id="create-estimated-time"
                            type="text"
                            name="estimated_time"
                            value="{{ old('estimated_time', '15 Menit') }}"
                            required
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus-visible:outline-teal-400"
                        >
                    </div>
                </div>

                <div>
                    <label for="create-module-title" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Judul Modul <span class="text-rose-700 dark:text-rose-300">*</span>
                    </label>
                    <input
                        id="create-module-title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Judul modul pembelajaran"
                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus-visible:outline-teal-400"
                    >
                </div>

                <div>
                    <label for="create-module-subtitle" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Subjudul Ringkas (Opsional)
                    </label>
                    <input
                        id="create-module-subtitle"
                        type="text"
                        name="subtitle"
                        value="{{ old('subtitle') }}"
                        placeholder="Ringkasan pengantar materi modul"
                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus-visible:outline-teal-400"
                    >
                </div>

                <div>
                    <label for="create-module-description" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Deskripsi Modul <span class="text-rose-700 dark:text-rose-300">*</span>
                    </label>
                    <textarea
                        id="create-module-description"
                        name="description"
                        rows="2"
                        required
                        placeholder="Tujuan dan ruang lingkup pembelajaran modul..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400 dark:focus-visible:outline-teal-400"
                    >{{ old('description') }}</textarea>
                </div>

                {{-- Pengaturan Banner Modul --}}
                <fieldset class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/60">
                    <div class="flex items-center justify-between gap-2">
                        <legend class="text-xs font-bold text-slate-900 dark:text-white">Banner Gambar Modul</legend>
                        <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300">Rasio 800 x 320 px</span>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-1.5 rounded-lg bg-slate-200/80 p-1 dark:bg-slate-800">
                        <button
                            type="button"
                            @click="createBannerMode = 'auto'"
                            :aria-pressed="(createBannerMode === 'auto').toString()"
                            :class="createBannerMode === 'auto' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 hover:text-slate-950 dark:text-slate-300'"
                            class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
                        >
                            Otomatis
                        </button>
                        <button
                            type="button"
                            @click="createBannerMode = 'upload'"
                            :aria-pressed="(createBannerMode === 'upload').toString()"
                            :class="createBannerMode === 'upload' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 hover:text-slate-950 dark:text-slate-300'"
                            class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
                        >
                            Unggah
                        </button>
                        <button
                            type="button"
                            @click="createBannerMode = 'url'"
                            :aria-pressed="(createBannerMode === 'url').toString()"
                            :class="createBannerMode === 'url' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 hover:text-slate-950 dark:text-slate-300'"
                            class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
                        >
                            Tautan URL
                        </button>
                    </div>
                    <input type="hidden" name="banner_mode" :value="createBannerMode">

                    <div x-show="createBannerMode === 'auto'" class="mt-3 flex flex-col gap-2">
                        <p class="text-xs text-slate-600 dark:text-slate-300">
                            Banner SVG dibuat otomatis berdasarkan judul dan nomor modul.
                        </p>
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                            <template x-for="th in [
                                { id: 'teal', name: 'Teal', color: '#0f766e' },
                                { id: 'amber', name: 'Amber', color: '#b45309' },
                                { id: 'rose', name: 'Rose', color: '#be123c' },
                                { id: 'indigo', name: 'Indigo', color: '#3730a3' },
                                { id: 'emerald', name: 'Emerald', color: '#047857' },
                                { id: 'purple', name: 'Purple', color: '#6b21a8' }
                            ]" :key="th.id">
                                <button
                                    type="button"
                                    @click="createBannerTheme = th.id"
                                    :aria-pressed="(createBannerTheme === th.id).toString()"
                                    :class="createBannerTheme === th.id ? 'border-teal-700 bg-white ring-2 ring-teal-700 dark:border-teal-400 dark:bg-slate-800 dark:ring-teal-400' : 'border-slate-200 bg-white/60 hover:bg-white dark:border-slate-700 dark:bg-slate-800/50'"
                                    class="flex min-h-11 flex-col items-center justify-center gap-1 rounded-lg border p-1.5 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
                                >
                                    <span class="h-4 w-4 rounded-sm" :style="'background-color: ' + th.color" aria-hidden="true"></span>
                                    <span class="w-full truncate text-center text-[10px] font-semibold text-slate-700 dark:text-slate-200" x-text="th.name"></span>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="banner_theme" :value="createBannerTheme">
                    </div>

                    <div x-show="createBannerMode === 'upload'" x-cloak class="mt-3 flex flex-col gap-1.5">
                        <label for="create-banner-file" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                            Pilih Berkas Gambar Banner (PNG, JPG, WEBP, SVG)
                        </label>
                        <input
                            id="create-banner-file"
                            type="file"
                            name="banner_file"
                            accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    createUploadedFileName = file.name;
                                    createBannerPreviewUrl = URL.createObjectURL(file);
                                }
                            "
                            class="w-full rounded-lg border border-slate-300 bg-white p-1.5 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-teal-800 hover:file:bg-teal-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:file:bg-teal-950 dark:file:text-teal-200"
                        >
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">Ukuran maksimal 5 MB.</p>
                    </div>

                    <div x-show="createBannerMode === 'url'" x-cloak class="mt-3 flex flex-col gap-1.5">
                        <label for="create-banner-url" class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                            URL atau Path Gambar Banner
                        </label>
                        <input
                            id="create-banner-url"
                            type="text"
                            name="banner_image"
                            placeholder="/images/banners/banner-module-1.svg atau https://..."
                            @input="createBannerPreviewUrl = $event.target.value"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                        >
                    </div>
                </fieldset>

                {{-- Subbab Pertama Berformat PDF Flipbook --}}
                <fieldset class="flex flex-col gap-3 rounded-xl border border-teal-200 bg-teal-50/50 p-4 dark:border-teal-800/70 dark:bg-teal-950/30">
                    <div class="flex items-center justify-between gap-2">
                        <legend class="text-xs font-bold text-teal-900 dark:text-teal-200">
                            Subbab Pertama (Format PDF Flipbook)
                        </legend>
                        <span class="rounded bg-teal-700 px-2 py-0.5 text-[10px] font-semibold text-white dark:bg-teal-500 dark:text-slate-950">
                            Wajib PDF
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                        <div>
                            <label for="create-topic-code" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Kode Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                            </label>
                            <input
                                id="create-topic-code"
                                type="text"
                                name="topic_code"
                                value="{{ old('topic_code', ($modules->max('module_number') + 1) . '.1') }}"
                                required
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                            >
                        </div>
                        <div class="sm:col-span-2">
                            <label for="create-topic-title" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Judul Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                            </label>
                            <input
                                id="create-topic-title"
                                type="text"
                                name="topic_title"
                                value="{{ old('topic_title') }}"
                                required
                                placeholder="Judul subbab materi"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="create-topic-pdf" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Berkas PDF Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                        </label>
                        <div class="relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-400 bg-white px-4 py-5 text-center transition-colors hover:border-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:hover:border-teal-400">
                            <input
                                id="create-topic-pdf"
                                type="file"
                                name="pdf_file"
                                accept="application/pdf,.pdf"
                                required
                                @change="
                                    const file = $event.target.files[0];
                                    createPdfName = file ? file.name : '';
                                    createPdfSize = file ? formatFileSize(file.size) : '';
                                "
                                class="absolute inset-0 h-full w-full opacity-0"
                            >
                            <svg class="h-7 w-7 text-teal-700 dark:text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="9" y1="15" x2="15" y2="15"/>
                                <line x1="12" y1="12" x2="12" y2="18"/>
                            </svg>
                            <p class="mt-2 text-xs font-semibold text-slate-800 dark:text-slate-100">
                                Pilih atau seret berkas PDF ke sini
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-300">
                                Format .pdf, ukuran maksimal 20 MB. Otomatis ditampilkan sebagai flipbook.
                            </p>
                            <p
                                x-show="createPdfName"
                                x-cloak
                                class="mt-2.5 rounded-md border border-teal-200 bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-900 dark:border-teal-700 dark:bg-teal-950 dark:text-teal-200"
                            >
                                <span x-text="createPdfName"></span>
                                <span x-show="createPdfSize" class="font-normal"> (<span x-text="createPdfSize"></span>)</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="create-topic-youtube" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            ID atau Tautan Video YouTube Pendamping (Opsional)
                        </label>
                        <input
                            id="create-topic-youtube"
                            type="text"
                            name="youtube_video_id"
                            value="{{ old('youtube_video_id') }}"
                            placeholder="Contoh: v3F4QW_h32M atau https://youtu.be/..."
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-400"
                        >
                    </div>
                </fieldset>

                <button
                    type="submit"
                    :disabled="submittingCreate"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:opacity-60 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400 dark:focus-visible:outline-teal-400"
                >
                    <span x-show="!submittingCreate">Unggah &amp; Publikasikan Modul</span>
                    <span x-show="submittingCreate" x-cloak>Mengunggah berkas PDF...</span>
                </button>
            </form>
        </section>

        {{-- Kolom Kanan (7 kolom): Daftar Modul & Subbab Aktif --}}
        <section aria-labelledby="active-modules-heading" class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6 xl:col-span-7">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div>
                    <h3 id="active-modules-heading" class="text-base font-bold text-slate-900 dark:text-white">
                        Daftar Modul &amp; Subbab Aktif
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Tambah subbab PDF baru, unggah atau ganti berkas PDF pada subbab yang sudah ada, atau perbarui banner modul.
                    </p>
                </div>
                <span class="rounded-md border border-teal-200 bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-800 dark:border-teal-800 dark:bg-teal-950 dark:text-teal-200">
                    {{ $modules->count() }} Modul
                </span>
            </div>

            <div class="flex flex-col gap-5">
                @forelse($modules as $mod)
                @php
                    $modPdfCount = $mod->topics->filter(fn ($t) => $t->hasPdf())->count();
                    $bannerSrc = $mod->banner_image ?: '/images/banners/banner-module-' . (($mod->module_number % 4) ?: 4) . '.svg';
                @endphp
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50/70 dark:border-slate-700 dark:bg-slate-900/50">
                    {{-- Banner Modul --}}
                    <div class="relative aspect-[5/1] min-h-[96px] w-full overflow-hidden bg-slate-900">
                        <img
                            src="{{ $bannerSrc }}"
                            alt="Banner Modul {{ $mod->module_number }}: {{ $mod->title }}"
                            width="800"
                            height="320"
                            class="h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 flex items-end justify-between gap-2 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent p-3">
                            <span class="rounded-md bg-slate-950/80 px-2.5 py-1 text-xs font-bold text-white">
                                Modul {{ str_pad((string) $mod->module_number, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <button
                                type="button"
                                @click="
                                    bannerModule = {
                                        id: {{ $mod->id }},
                                        number: '{{ $mod->module_number }}',
                                        title: '{{ addslashes($mod->title) }}',
                                        subtitle: '{{ addslashes($mod->subtitle ?? '') }}',
                                        currentBanner: '{{ addslashes($mod->banner_image ?? '') }}'
                                    };
                                    editBannerMode = 'auto';
                                    editBannerPreviewUrl = '{{ addslashes($mod->banner_image ?? '') }}';
                                    editUploadedFileName = '';
                                    bannerModalOpen = true;
                                "
                                class="inline-flex min-h-11 items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-900 shadow-xs transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
                            >
                                Ubah Banner
                            </button>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        {{-- Judul & Tindakan Modul --}}
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <span class="font-semibold text-teal-700 dark:text-teal-300">{{ $mod->estimated_time }}</span>
                                    <span aria-hidden="true">&middot;</span>
                                    <span>{{ $mod->topics->count() }} Subbab</span>
                                    <span aria-hidden="true">&middot;</span>
                                    <span>{{ $modPdfCount }} PDF Flipbook</span>
                                </div>
                                <h4 class="mt-1 text-base font-bold text-slate-900 dark:text-white">{{ $mod->title }}</h4>
                                @if($mod->subtitle)
                                <p class="mt-0.5 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $mod->subtitle }}</p>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    @click="addingTopicTo = (addingTopicTo === {{ $mod->id }} ? null : {{ $mod->id }})"
                                    :aria-expanded="(addingTopicTo === {{ $mod->id }}).toString()"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-800 transition-colors hover:bg-teal-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-teal-500 dark:bg-teal-950/70 dark:text-teal-200 dark:hover:bg-teal-900"
                                >
                                    + Tambah Subbab PDF
                                </button>
                                <button
                                    type="button"
                                    @click="deleteModalOpen = true; moduleToDelete = { id: {{ $mod->id }}, number: '{{ $mod->module_number }}', title: '{{ addslashes($mod->title) }}' }"
                                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-800 transition-colors hover:bg-rose-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-200 dark:hover:bg-rose-900"
                                >
                                    Hapus Modul
                                </button>
                            </div>
                        </div>

                        {{-- Form Tambah Subbab PDF Inline --}}
                        <div
                            x-show="addingTopicTo === {{ $mod->id }}"
                            x-cloak
                            class="mt-4 rounded-xl border border-teal-300 bg-white p-4 dark:border-teal-700 dark:bg-slate-800"
                        >
                            <h5 class="text-xs font-bold text-teal-800 dark:text-teal-200">
                                Tambah Subbab PDF Baru ke Modul {{ $mod->module_number }}
                            </h5>
                            <form
                                method="POST"
                                action="{{ route('admin.modules.topics.store', $mod->id) }}"
                                enctype="multipart/form-data"
                                @submit="submittingTopicId = {{ $mod->id }}"
                                class="mt-3 flex flex-col gap-3"
                            >
                                @csrf
                                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                                    <div>
                                        <label for="add-topic-code-{{ $mod->id }}" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-200">
                                            Kode Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                                        </label>
                                        <input
                                            id="add-topic-code-{{ $mod->id }}"
                                            type="text"
                                            name="topic_code"
                                            value="{{ $mod->module_number }}.{{ $mod->topics->count() + 1 }}"
                                            required
                                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="add-topic-title-{{ $mod->id }}" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-200">
                                            Judul Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                                        </label>
                                        <input
                                            id="add-topic-title-{{ $mod->id }}"
                                            type="text"
                                            name="title"
                                            required
                                            placeholder="Judul subbab materi"
                                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label for="add-topic-pdf-{{ $mod->id }}" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        Berkas PDF Subbab (.pdf, maks. 20 MB) <span class="text-rose-700 dark:text-rose-300">*</span>
                                    </label>
                                    <input
                                        id="add-topic-pdf-{{ $mod->id }}"
                                        type="file"
                                        name="pdf_file"
                                        accept="application/pdf,.pdf"
                                        required
                                        @change="topicPdfNames[{{ $mod->id }}] = $event.target.files[0] ? $event.target.files[0].name : ''"
                                        class="w-full rounded-lg border border-slate-300 bg-white p-1.5 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-teal-800 hover:file:bg-teal-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:file:bg-teal-950 dark:file:text-teal-200"
                                    >
                                </div>

                                <div>
                                    <label for="add-topic-yt-{{ $mod->id }}" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        ID atau Tautan YouTube Pendamping (Opsional)
                                    </label>
                                    <input
                                        id="add-topic-yt-{{ $mod->id }}"
                                        type="text"
                                        name="youtube_video_id"
                                        placeholder="Contoh: e7zP-b-YnS0 atau https://youtu.be/..."
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                                    >
                                </div>

                                <div class="flex flex-wrap justify-end gap-2 pt-1">
                                    <button
                                        type="button"
                                        @click="addingTopicTo = null"
                                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="submittingTopicId === {{ $mod->id }}"
                                        class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:opacity-60 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                                    >
                                        <span x-show="submittingTopicId !== {{ $mod->id }}">Simpan Subbab PDF</span>
                                        <span x-show="submittingTopicId === {{ $mod->id }}" x-cloak>Mengunggah...</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Daftar Subbab dalam Modul --}}
                        <div class="mt-4 flex flex-col gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                            @forelse($mod->topics as $tp)
                            @php
                                $hasPdf = $tp->hasPdf();
                                $pdfSizeLabel = $tp->formattedPdfSize();
                            @endphp
                            <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-3.5 dark:border-slate-700 dark:bg-slate-800">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800 dark:bg-slate-700 dark:text-slate-100">
                                            {{ $tp->topic_code }}
                                        </span>
                                        @if($hasPdf)
                                        <span class="rounded border border-teal-200 bg-teal-50 px-2 py-0.5 text-[11px] font-semibold text-teal-800 dark:border-teal-700 dark:bg-teal-950 dark:text-teal-200">
                                            PDF Flipbook{{ $pdfSizeLabel ? ' · ' . $pdfSizeLabel : '' }}
                                        </span>
                                        @else
                                        <span class="rounded border border-amber-300 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200">
                                            HTML Lama (Belum Ada PDF)
                                        </span>
                                        @endif
                                        @if($tp->youtube_video_id)
                                        <span class="rounded border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-800 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200">
                                            Video YouTube
                                        </span>
                                        @endif
                                    </div>
                                    <p class="mt-1.5 text-sm font-semibold text-slate-900 dark:text-white">{{ $tp->title }}</p>
                                    @if($hasPdf && $tp->pdf_original_name)
                                    <p class="mt-0.5 break-all text-xs text-slate-600 dark:text-slate-300" title="{{ $tp->pdf_original_name }}">
                                        Berkas: {{ $tp->pdf_original_name }}
                                    </p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-2.5 dark:border-slate-700/80">
                                    <button
                                        type="button"
                                        @click="openEditTopicModal({
                                            id: {{ $tp->id }},
                                            moduleNumber: '{{ $mod->module_number }}',
                                            code: '{{ addslashes($tp->topic_code) }}',
                                            title: '{{ addslashes($tp->title) }}',
                                            youtube: '{{ addslashes($tp->youtube_video_id ?? '') }}',
                                            currentPdfName: '{{ addslashes($tp->pdf_original_name ?? '') }}',
                                            currentPdfSize: '{{ addslashes($pdfSizeLabel ?? '') }}'
                                        })"
                                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-700"
                                    >
                                        {{ $hasPdf ? 'Ubah / Ganti PDF' : 'Unggah PDF' }}
                                    </button>
                                    <a
                                        href="{{ route('modules.topic', [$mod->id, $tp->id]) }}"
                                        class="inline-flex min-h-11 items-center rounded-lg border border-teal-300 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-800 transition-colors hover:bg-teal-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-teal-700 dark:bg-teal-950 dark:text-teal-200 dark:hover:bg-teal-900"
                                    >
                                        {{ $hasPdf ? 'Buka Flipbook' : 'Lihat' }}
                                    </a>
                                    <button
                                        type="button"
                                        @click="openDeleteTopicModal({ id: {{ $tp->id }}, code: '{{ addslashes($tp->topic_code) }}', title: '{{ addslashes($tp->title) }}' })"
                                        aria-label="Hapus subbab {{ $tp->topic_code }}"
                                        title="Hapus subbab"
                                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-700 dark:hover:bg-rose-950 dark:hover:text-rose-200"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                Modul ini belum memiliki subbab PDF. Klik tombol <strong>+ Tambah Subbab PDF</strong> di atas untuk mengunggah berkas PDF pertama.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </article>
                @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/50">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Belum ada modul edukasi yang tersimpan</p>
                    <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                        Mulai dengan mengisi formulir Unggah Modul Edukasi Baru di sebelah kiri atau impor daftar modul dari berkas Excel.
                    </p>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- MODAL EDIT / UNGGAH PDF SUBBAB --}}
    <div
        x-show="editTopicModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="edit-topic-modal-title"
        class="fixed inset-0 z-99999 flex items-center justify-center bg-slate-950/65 p-4"
    >
        <div
            @click.outside="editTopicModalOpen = false"
            class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div>
                    <h3 id="edit-topic-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                        Perbarui Subbab &amp; Berkas PDF Flipbook
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Modul <span x-text="editTopic.moduleNumber"></span> &middot; Subbab <span x-text="editTopic.code"></span>
                    </p>
                </div>
                <button
                    type="button"
                    @click="editTopicModalOpen = false"
                    aria-label="Tutup modal ubah subbab"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    &times;
                </button>
            </div>

            <form
                method="POST"
                :action="'{{ url('/admin/topics') }}/' + editTopic.id"
                enctype="multipart/form-data"
                @submit="submittingEditTopic = true"
                class="mt-4 flex flex-col gap-4"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <label for="edit-topic-code" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Kode Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                        </label>
                        <input
                            id="edit-topic-code"
                            type="text"
                            name="topic_code"
                            x-model="editTopic.code"
                            required
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        >
                    </div>
                    <div class="sm:col-span-2">
                        <label for="edit-topic-title" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Judul Subbab <span class="text-rose-700 dark:text-rose-300">*</span>
                        </label>
                        <input
                            id="edit-topic-title"
                            type="text"
                            name="title"
                            x-model="editTopic.title"
                            required
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        >
                    </div>
                </div>

                <div>
                    <label for="edit-topic-pdf" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Unggah Berkas PDF Baru (.pdf, maks. 20 MB)
                    </label>
                    <p x-show="editTopic.currentPdfName" class="mb-2 rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs text-teal-900 dark:border-teal-800 dark:bg-teal-950/60 dark:text-teal-200">
                        PDF saat ini: <strong x-text="editTopic.currentPdfName"></strong>
                        <span x-show="editTopic.currentPdfSize"> (<span x-text="editTopic.currentPdfSize"></span>)</span>. Kosongkan jika tidak ingin mengganti berkas PDF.
                    </p>
                    <p x-show="!editTopic.currentPdfName" class="mb-2 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-700 dark:bg-amber-950/60 dark:text-amber-200">
                        Subbab ini belum memiliki PDF flipbook. Pilih berkas PDF di bawah agar tampil sebagai flipbook.
                    </p>
                    <input
                        id="edit-topic-pdf"
                        type="file"
                        name="pdf_file"
                        accept="application/pdf,.pdf"
                        @change="
                            const f = $event.target.files[0];
                            editTopicNewPdfName = f ? f.name : '';
                            editTopicNewPdfSize = f ? formatFileSize(f.size) : '';
                        "
                        class="w-full rounded-lg border border-slate-300 bg-white p-1.5 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-teal-800 hover:file:bg-teal-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:file:bg-teal-950 dark:file:text-teal-200"
                    >
                    <p x-show="editTopicNewPdfName" x-cloak class="mt-1.5 text-xs font-semibold text-teal-700 dark:text-teal-300">
                        Berkas baru terpilih: <span x-text="editTopicNewPdfName"></span> (<span x-text="editTopicNewPdfSize"></span>)
                    </p>
                </div>

                <div>
                    <label for="edit-topic-youtube" class="mb-1 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        ID atau Tautan Video YouTube Pendamping (Opsional)
                    </label>
                    <input
                        id="edit-topic-youtube"
                        type="text"
                        name="youtube_video_id"
                        x-model="editTopic.youtube"
                        placeholder="Kosongkan jika tidak memakai video"
                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                    <button
                        type="button"
                        @click="editTopicModalOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="submittingEditTopic"
                        class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:opacity-60 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                    >
                        <span x-show="!submittingEditTopic">Simpan Perubahan</span>
                        <span x-show="submittingEditTopic" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS MODUL --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-module-modal-title"
        class="fixed inset-0 z-99999 flex items-center justify-center bg-slate-950/65 p-4"
    >
        <div
            @click.outside="deleteModalOpen = false"
            class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900"
        >
            <h3 id="delete-module-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                Hapus Modul Edukasi
            </h3>
            <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                Hapus <strong>Modul <span x-text="moduleToDelete.number"></span>: "<span x-text="moduleToDelete.title"></span>"</strong>? Seluruh subbab, berkas PDF flipbook, dan bank soal evaluasi yang terhubung pada modul ini akan ikut dihapus permanen.
            </p>

            <form method="POST" :action="'{{ url('/admin/modules') }}/' + moduleToDelete.id" class="mt-5 flex flex-wrap items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    @click="deleteModalOpen = false"
                    class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex min-h-11 items-center rounded-lg bg-rose-700 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700"
                >
                    Ya, Hapus Modul
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS SUBBAB --}}
    <div
        x-show="deleteTopicModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-topic-modal-title"
        class="fixed inset-0 z-99999 flex items-center justify-center bg-slate-950/65 p-4"
    >
        <div
            @click.outside="deleteTopicModalOpen = false"
            class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900"
        >
            <h3 id="delete-topic-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                Hapus Subbab Materi
            </h3>
            <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                Hapus subbab <strong><span x-text="topicToDelete.code"></span> (<span x-text="topicToDelete.title"></span>)</strong> beserta berkas PDF flipbook miliknya? Tindakan ini tidak dapat dibatalkan.
            </p>

            <form method="POST" :action="'{{ url('/admin/topics') }}/' + topicToDelete.id" class="mt-5 flex flex-wrap items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    @click="deleteTopicModalOpen = false"
                    class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex min-h-11 items-center rounded-lg bg-rose-700 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700"
                >
                    Ya, Hapus Subbab
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL IMPOR EXCEL --}}
    <div
        x-show="uploadExcelModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="import-excel-modal-title"
        class="fixed inset-0 z-99999 flex items-center justify-center bg-slate-950/65 p-4"
    >
        <div
            @click.outside="uploadExcelModalOpen = false"
            class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div>
                    <h3 id="import-excel-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                        Impor Struktur Modul &amp; Soal dari Excel
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                        Unggah berkas .xlsx sesuai template SIKERA, lalu lengkapi PDF flipbook pada tiap subbab.
                    </p>
                </div>
                <button
                    type="button"
                    @click="uploadExcelModalOpen = false"
                    aria-label="Tutup modal impor Excel"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    &times;
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('admin.modules.import') }}"
                enctype="multipart/form-data"
                @submit="isUploadingExcel = true"
                class="mt-4 flex flex-col gap-4"
            >
                @csrf

                <div class="rounded-lg border border-teal-200 bg-teal-50 p-3.5 text-xs leading-relaxed text-teal-900 dark:border-teal-800 dark:bg-teal-950/60 dark:text-teal-200">
                    Pastikan berkas menggunakan 3 lembar resmi (<strong>Modul Utama</strong>, <strong>Submateri</strong>, dan <strong>Bank Soal</strong>).
                    <a href="{{ route('admin.modules.template') }}" class="ml-1 font-bold underline">Unduh Template Excel</a>
                </div>

                <div>
                    <label for="excel-import-file" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Berkas Excel (.xlsx / .xls, maks. 15 MB) <span class="text-rose-700 dark:text-rose-300">*</span>
                    </label>
                    <input
                        id="excel-import-file"
                        type="file"
                        name="excel_file"
                        accept=".xlsx,.xls"
                        required
                        @change="selectedExcelFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                        class="w-full rounded-lg border border-slate-300 bg-white p-1.5 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-teal-800 hover:file:bg-teal-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:file:bg-teal-950 dark:file:text-teal-200"
                    >
                    <p x-show="selectedExcelFileName" x-cloak class="mt-1.5 text-xs font-semibold text-teal-700 dark:text-teal-300">
                        Berkas terpilih: <span x-text="selectedExcelFileName"></span>
                    </p>
                </div>

                <label for="overwrite_checkbox" class="inline-flex min-h-11 items-center gap-2.5 text-xs font-medium text-slate-700 dark:text-slate-200">
                    <input
                        type="checkbox"
                        name="overwrite"
                        id="overwrite_checkbox"
                        value="1"
                        checked
                        class="h-4 w-4 rounded border-slate-300 text-teal-700 focus-visible:outline-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800"
                    >
                    Perbarui data jika nomor modul sudah ada
                </label>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                    <button
                        type="button"
                        @click="uploadExcelModalOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="isUploadingExcel"
                        class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 disabled:opacity-60 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                    >
                        <span x-show="!isUploadingExcel">Mulai Impor Data</span>
                        <span x-show="isUploadingExcel" x-cloak>Memproses berkas...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL UBAH BANNER MODUL --}}
    <div
        x-show="bannerModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="banner-modal-title"
        class="fixed inset-0 z-99999 flex items-center justify-center bg-slate-950/65 p-4"
    >
        <div
            @click.outside="bannerModalOpen = false"
            class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div class="min-w-0">
                    <h3 id="banner-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                        Ubah Banner Modul <span x-text="bannerModule.number"></span>
                    </h3>
                    <p class="mt-0.5 truncate text-xs text-slate-600 dark:text-slate-300" x-text="bannerModule.title"></p>
                </div>
                <button
                    type="button"
                    @click="bannerModalOpen = false"
                    aria-label="Tutup modal ubah banner"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    &times;
                </button>
            </div>

            <form
                method="POST"
                :action="'{{ url('/admin/modules') }}/' + bannerModule.id + '/banner'"
                enctype="multipart/form-data"
                class="mt-4 flex flex-col gap-4"
            >
                @csrf

                <div class="grid grid-cols-3 gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-slate-800">
                    <button
                        type="button"
                        @click="editBannerMode = 'auto'"
                        :aria-pressed="(editBannerMode === 'auto').toString()"
                        :class="editBannerMode === 'auto' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 dark:text-slate-300'"
                        class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors"
                    >
                        Otomatis
                    </button>
                    <button
                        type="button"
                        @click="editBannerMode = 'upload'"
                        :aria-pressed="(editBannerMode === 'upload').toString()"
                        :class="editBannerMode === 'upload' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 dark:text-slate-300'"
                        class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors"
                    >
                        Unggah
                    </button>
                    <button
                        type="button"
                        @click="editBannerMode = 'url'"
                        :aria-pressed="(editBannerMode === 'url').toString()"
                        :class="editBannerMode === 'url' ? 'bg-white text-teal-800 shadow-xs dark:bg-slate-700 dark:text-white' : 'text-slate-700 dark:text-slate-300'"
                        class="min-h-11 rounded-md px-2 py-1.5 text-xs font-semibold transition-colors"
                    >
                        URL
                    </button>
                </div>
                <input type="hidden" name="banner_mode" :value="editBannerMode">

                <div x-show="editBannerMode === 'auto'" class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                    <template x-for="th in [
                        { id: 'teal', name: 'Teal', color: '#0f766e' },
                        { id: 'amber', name: 'Amber', color: '#b45309' },
                        { id: 'rose', name: 'Rose', color: '#be123c' },
                        { id: 'indigo', name: 'Indigo', color: '#3730a3' },
                        { id: 'emerald', name: 'Emerald', color: '#047857' },
                        { id: 'purple', name: 'Purple', color: '#6b21a8' }
                    ]" :key="th.id">
                        <button
                            type="button"
                            @click="editBannerTheme = th.id"
                            :aria-pressed="(editBannerTheme === th.id).toString()"
                            :class="editBannerTheme === th.id ? 'border-teal-700 ring-2 ring-teal-700 dark:border-teal-400 dark:ring-teal-400' : 'border-slate-200 dark:border-slate-700'"
                            class="flex min-h-11 flex-col items-center justify-center gap-1 rounded-lg border p-1.5"
                        >
                            <span class="h-4 w-4 rounded-sm" :style="'background-color: ' + th.color" aria-hidden="true"></span>
                            <span class="text-[10px] font-semibold text-slate-700 dark:text-slate-200" x-text="th.name"></span>
                        </button>
                    </template>
                    <input type="hidden" name="banner_theme" :value="editBannerTheme">
                </div>

                <div x-show="editBannerMode === 'upload'" x-cloak>
                    <label for="edit-banner-file" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Berkas Gambar Banner Baru (Maks. 5 MB)
                    </label>
                    <input
                        id="edit-banner-file"
                        type="file"
                        name="banner_file"
                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                        class="w-full rounded-lg border border-slate-300 bg-white p-1.5 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-teal-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                    >
                </div>

                <div x-show="editBannerMode === 'url'" x-cloak>
                    <label for="edit-banner-url" class="mb-1.5 block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        URL atau Path Gambar Banner
                    </label>
                    <input
                        id="edit-banner-url"
                        type="text"
                        name="banner_image"
                        :value="bannerModule.currentBanner"
                        placeholder="/images/banners/banner-module-1.svg"
                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                    <button
                        type="button"
                        @click="bannerModalOpen = false"
                        class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex min-h-11 items-center rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400"
                    >
                        Simpan Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
