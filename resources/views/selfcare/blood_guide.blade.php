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

        {{-- Klasifikasi Volume Darah Menstruasi (FIGO & Kemenkes RI) --}}
        <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-800 space-y-6">
            <div>
                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-[11px] font-bold text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800 uppercase tracking-wide">
                    Standar Klinis FIGO &amp; Kemenkes RI
                </span>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mt-2">Klasifikasi Volume Darah Menstruasi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                    Dalam ginekologi dan kebidanan (FIGO / <em>International Federation of Gynecology and Obstetrics</em>), volume darah diukur berdasarkan total pengeluaran darah per satu siklus menstruasi.
                </p>
            </div>

            {{-- 1. Tabel Kategori Volume --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-200 font-bold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="p-3.5">Kategori Volume</th>
                            <th class="p-3.5">Rerata Volume (ml)</th>
                            <th class="p-3.5">Karakteristik &amp; Indikator Klinis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                            <td class="p-3.5 font-bold text-amber-700 dark:text-amber-400">
                                Hipomenore<br><span class="text-[10px] font-normal text-gray-400">(Sangat Sedikit)</span>
                            </td>
                            <td class="p-3.5 font-semibold">&lt; 5 &ndash; 10 ml</td>
                            <td class="p-3.5 leading-relaxed">Hanya bercak (<em>spotting</em>), durasi haid sangat singkat (&lt; 2 hari), pembalut jarang terisi penuh.</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 bg-teal-50/30 dark:bg-teal-950/20">
                            <td class="p-3.5 font-bold text-teal-700 dark:text-teal-400">
                                Normal (Eumenore)<br><span class="text-[10px] font-normal text-gray-400">(Fisiologis Sehat)</span>
                            </td>
                            <td class="p-3.5 font-semibold text-teal-800 dark:text-teal-200">30 &ndash; 40 ml</td>
                            <td class="p-3.5 leading-relaxed">Rentang fisiologis berkisar 5 &ndash; 80 ml per siklus, ganti pembalut teratur 3 &ndash; 5 kali sehari.</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 bg-rose-50/30 dark:bg-rose-950/20">
                            <td class="p-3.5 font-bold text-rose-700 dark:text-rose-400">
                                Menoragia / HMB<br><span class="text-[10px] font-normal text-gray-400">(Sangat Banyak)</span>
                            </td>
                            <td class="p-3.5 font-semibold text-rose-800 dark:text-rose-200">&gt; 80 ml</td>
                            <td class="p-3.5 leading-relaxed"><em>Heavy Menstrual Bleeding</em> (HMB): pembalut penuh basah tiap 1 &ndash; 2 jam, ada gumpalan darah berdiameter &gt; 2,5 cm, sering memicu anemia defisiensi besi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. Metode Skoring PBAC --}}
            <div class="rounded-xl border border-teal-200 bg-gradient-to-br from-teal-50/60 to-white p-5 dark:border-teal-900/50 dark:bg-gray-900 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="rounded-lg bg-teal-600 p-1 text-white">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </span>
                        Metode Penilaian Klinis: Skoring PBAC (Higham et al.)
                    </h4>
                    <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 bg-teal-100 dark:bg-teal-900/50 px-2 py-0.5 rounded-full">
                        Pictorial Blood Loss
                    </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    Karena sulit mengukur mililiter darah secara langsung di rumah, penelitian ginekologi menggunakan bagan visual <strong>Higham PBAC</strong> untuk menghitung poin pembalut dan gumpalan:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Poin Pembalut --}}
                    <div class="rounded-xl bg-white p-4 border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-2">
                        <p class="font-bold text-gray-800 dark:text-gray-200">1. Poin Pembalut / Tampon</p>
                        <ul class="space-y-1.5 text-gray-600 dark:text-gray-300 text-[11px]">
                            <li class="flex justify-between items-center pb-1 border-b border-gray-100 dark:border-gray-700">
                                <span>Bernoda sedikit (terisi &le; 1/3):</span>
                                <span class="font-bold text-teal-600 dark:text-teal-400">1 Poin</span>
                            </li>
                            <li class="flex justify-between items-center pb-1 border-b border-gray-100 dark:border-gray-700">
                                <span>Bernoda sedang (terisi &approx; 1/2):</span>
                                <span class="font-bold text-teal-600 dark:text-teal-400">5 Poin</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Basah jenuh (terisi penuh basah):</span>
                                <span class="font-bold text-teal-600 dark:text-teal-400">20 Poin</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Poin Gumpalan --}}
                    <div class="rounded-xl bg-white p-4 border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-2">
                        <p class="font-bold text-gray-800 dark:text-gray-200">2. Poin Gumpalan Darah (Clots)</p>
                        <ul class="space-y-1.5 text-gray-600 dark:text-gray-300 text-[11px]">
                            <li class="flex justify-between items-center pb-1 border-b border-gray-100 dark:border-gray-700">
                                <span>Ukuran kecil (&lt; 2,5 cm):</span>
                                <span class="font-bold text-teal-600 dark:text-teal-400">1 Poin</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span>Ukuran besar (&ge; 2,5 cm / koin):</span>
                                <span class="font-bold text-teal-600 dark:text-teal-400">5 Poin</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Rumus & Cut-off --}}
                <div class="rounded-lg bg-gray-50 p-3 text-xs border border-gray-200/80 dark:bg-gray-800/50 dark:border-gray-700 space-y-1.5">
                    <p class="font-bold text-gray-800 dark:text-gray-200">Rumus Interpretasi Skor PBAC:</p>
                    <p class="font-mono text-[11px] text-teal-700 dark:text-teal-300">Total Skor PBAC = Jumlah Poin Pembalut + Jumlah Poin Gumpalan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 text-[11px]">
                        <div class="rounded p-2 bg-success-50 text-success-800 dark:bg-success-950/40 dark:text-success-300">
                            <strong>Skor &lt; 100 Poin:</strong> Volume darah normal (&lt; 80 ml per siklus).
                        </div>
                        <div class="rounded p-2 bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                            <strong>Skor &ge; 100 Poin:</strong> Mengindikasikan perdarahan menstruasi berlebih / menoragia (&ge; 80 ml) dengan akurasi &gt; 80%.
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Estimasi Praktis Pergantian Pembalut --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-800/40 space-y-3">
                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Estimasi Cepat Frekuensi Ganti Pembalut</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl border border-success-200 bg-success-50/50 p-3.5 dark:border-success-900/40 dark:bg-success-950/20">
                        <span class="font-bold text-success-700 dark:text-success-300 block mb-1">Pola Ringan / Normal:</span>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-[11px]">
                            Mengganti pembalut <strong>3 &ndash; 4 kali sehari</strong> dengan daya serap biasa. Pembalut tidak langsung penuh basah dalam 1-2 jam.
                        </p>
                    </div>
                    <div class="rounded-xl border border-error-200 bg-error-50/50 p-3.5 dark:border-error-900/40 dark:bg-error-950/20">
                        <span class="font-bold text-error-700 dark:text-error-300 block mb-1">Pola Berat / Waspada (Perlu Periksa):</span>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-[11px]">
                            Mengganti pembalut <strong>tiap 1 &ndash; 2 jam</strong> selama lebih dari dua jam berturut-turut, harus memakai pembalut ganda (<em>double pads</em>), atau sering tembus ke pakaian/seprai di malam hari.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
