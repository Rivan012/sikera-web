@extends('layouts.admin')
@section('title', 'Tips Higienitas Genitalia')
@section('page-title', 'Panduan Higienitas Organ Kewanitaan')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('selfcare.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Self-Care
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">4 Panduan Utama Higienitas Genitalia</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Langkah perawatan harian organ reproduksi untuk mencegah infeksi dan menjaga keseimbangan pH alami.</p>

        <div class="space-y-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">1</div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Basuh dari Depan ke Belakang</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Selalu membersihkan area genitalia dari arah vagina ke anus (depan ke belakang), bukan sebaliknya. Hal ini mencegah perpindahan bakteri usus seperti E. Coli dari rektum ke saluran kemih dan vagina, yang dapat menyebabkan infeksi saluran kemih (ISK).</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">2</div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Ganti Pembalut Secara Teratur</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Ganti pembalut minimal setiap 3-4 jam sekali saat aliran haid sedang deras, dan 4-6 jam saat ringan. Pembalut yang lembab terlalu lama menjadi tempat berkembang biak bakteri dan jamur yang memicu iritasi kulit dan bau tidak sedap.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">3</div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Pilih Pakaian Dalam yang Tepat</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Gunakan pakaian dalam berbahan katun yang menyerap keringat dan berukuran pas (tidak terlalu ketat). Bahan sintetis dan celana terlalu ketat menciptakan lingkungan lembab dan panas yang meningkatkan risiko infeksi jamur (kandidiasis).</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">4</div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Hindari Sabun Pewangi & Douching</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Vagina memiliki sistem pembersihan mandiri yang dijaga oleh bakteri baik Lactobacillus (menjaga pH asam 3.8-4.5). Penggunaan sabun antiseptik berpewangi atau douching (menyemprot cairan ke dalam vagina) secara rutin justru merusak flora normal ini dan memicu keputihan patologis. Cukup basuh area luar dengan air bersih.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
