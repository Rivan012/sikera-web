@extends('layouts.admin')
@section('title', 'Panduan Warna Darah Haid')
@section('page-title', 'Panduan Warna Darah Menstruasi')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('selfcare.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Self-Care
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Variasi Karakteristik Darah Menstruasi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Warna dan tekstur darah haid berubah sepanjang siklus. Berikut panduan medis untuk mengenali kondisi normal dan kondisi yang memerlukan pemeriksaan.</p>

        <div class="space-y-5">
            <div class="flex items-start gap-4 rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-500"></div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Merah Terang</h4>
                    <span class="inline-block mt-1 rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Normal / Fisiologis</span>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Darah segar dengan aliran lancar, biasanya muncul di hari ke-1 hingga ke-3 siklus haid. Menandakan peluruhan endometrium yang sehat dan sirkulasi darah yang baik. Ini adalah warna paling umum saat puncak menstruasi.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                <div class="flex-shrink-0 h-10 w-10 rounded-full" style="background-color: #5C2D06;"></div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Cokelat Gelap / Kehitaman</h4>
                    <span class="inline-block mt-1 rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Normal / Oksidasi</span>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Darah sisa yang mengalami proses oksidasi (kontak dengan udara) saat keluar perlahan dari rahim. Sering muncul di awal atau akhir siklus. Perubahan warna menjadi cokelat ini bersifat fisiologis dan bukan tanda gangguan.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-pink-300"></div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white">Merah Muda Pucat</h4>
                    <span class="inline-block mt-1 rounded-full bg-warning-50 px-2 py-0.5 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">Perlu Perhatian</span>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Dapat terjadi akibat kadar hormon estrogen yang relatif rendah, bercak flek (spotting) di luar siklus, atau campuran darah dengan cairan serviks. Jika terjadi secara berulang, konsultasikan dengan dokter untuk evaluasi hormonal.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 rounded-xl border border-error-200 bg-error-50/50 p-5 dark:border-error-800 dark:bg-error-500/5">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-900 dark:bg-gray-600"></div>
                <div>
                    <h4 class="text-base font-semibold text-error-700 dark:text-error-400">Hitam Pekat + Gumpalan Besar + Bau Menyengat</h4>
                    <span class="inline-block mt-1 rounded-full bg-error-100 px-2 py-0.5 text-xs font-medium text-error-700 dark:bg-error-500/20 dark:text-error-400">Segera Periksa ke Dokter</span>
                    <p class="mt-2 text-sm text-error-600 dark:text-error-300 leading-relaxed">Jika darah berwarna sangat gelap disertai gumpalan lebih besar dari koin, aroma busuk menyengat, atau demam tinggi, segera periksakan diri ke fasilitas kesehatan. Ini bisa menjadi tanda infeksi, endometriosis, atau kondisi medis serius lainnya.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
