# SIKERA (Sistem Informasi Kesehatan Reproduksi Remaja)

[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.0-38bdf8.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**SIKERA** adalah platform edukasi kesehatan reproduksi dan instrumen evaluasi riset intervensi bagi mahasiswa baru di lingkungan universitas (Universitas Bengkulu). Aplikasi ini dirancang untuk meningkatkan literasi kesehatan reproduksi remaja melalui materi ilmiah komprehensif tanpa vulgaritas, sarana pemantauan mandiri (*self-care tracker*), rubrik sosial kasus kampus, gamifikasi harian, serta evaluasi kognitif terukur (*Pre-Test*, *Post-Test*, dan *N-Gain score*) yang terintegrasi ke Google Sheets dan SPSS.

---

## 🌟 Fitur Utama Sistem

### 1. Mahasiswa (Responden Riset)
* **Formulir Pendaftaran & Biodata Demografi**: Registrasi mahasiswa baru mencakup inisial, usia, jenis kelamin, agama, program studi, fakultas, dan pendidikan terakhir.
* **Modul Pembelajaran & Evaluasi Per Modul (*Gating System*)**:
  * **Pre-Test Modul**: Wajib dikerjakan sebelum materi modul dapat dibuka.
  * **Materi Ilmiah Terpadu**: Teks komprehensif, infografis, dan video YouTube terintegrasi.
  * **Post-Test Modul**: Dikerjakan setelah seluruh materi modul selesai untuk menghitung peningkatan pemahaman (*N-Gain score*).
* **Self-Care Tools**:
  * **Pelacak Siklus Haid (Khusus Perempuan)**: Estimasi haid berikutnya, masa subur, ovulasi, pencatatan volume aliran, dan derajat nyeri haid (skala NRS).
  * **Kalkulator Indeks Massa Tubuh (IMT)**: Deteksi status gizi mandiri dan rekomendasi kesehatan hormonal.
  * **Panduan Visual Darah Haid & Higienitas**: Spektrum warna darah menstruasi (fisiologis vs patologis) dan 4 tips higienitas genitalia.
* **Projek Visual Poster Kaspro (KesproFeed)**: Galeri poster/pamflet digital kualitas HD dengan fitur unduh lokal dan *one-click share* ke WhatsApp.
* **Sosial & Perlindungan Kampus**:
  * **Studi Kasus Nyata Kampus**: Bedah kasus indekos (*living together*, *gaslighting*, KBGO, risiko IMS/KTD) beserta analisis hukum UU TPKS No. 12/2022 dan mitigasi medis.
  * **Forum Tanya Jawab Anonim**: Ruang konsultasi aman dua arah bersama konselor dan dosen PA.
* **Gamifikasi & Retensi Harian**:
  * **Trivia Harian & Streak Counter**: Kuis harian berhadiah poin.
  * **Mitos vs Fakta**: Uji pengetahuan seputar mitos kesehatan reproduksi.
  * **Secret Diary / Mood Log**: Jurnal catatan harian suasana hati.

---

### 2. Dosen Pembimbing Akademik (Dosen PA & Konselor)
* **Dashboard Statistik Kelas**: Pantau rata-rata skor Pre-Test, Post-Test, indeks *N-Gain*, serta persentase partisipasi mahasiswa bimbingan per fakultas.
* **Bahan Bimbingan & Konseling**: Akses langsung katalog modul materi edukasi dan respons pertanyaan konseling anonim mahasiswa.

---

### 3. Super Admin & Pengelola Riset (Terpisah & Terstruktur)
* **Dashboard Grafik & Evaluasi**: Grafik batang perbandingan skor Pre vs Post per modul, Radial Gauge N-Gain, dan KPI riset.
* **Daftar Pengguna**: Manajemen seluruh akun terdaftar dengan filter peran (*Mahasiswa, Dosen PA, Super Admin*) dan pencarian cepat.
* **Kelola Modul**: Unggah modul edukasi baru, tambah submateri (+ YouTube ID & teks ilmiah), dan hapus materi.
* **Lembar Data**: *Live Spreadsheet View* data mentah responden, skor evaluasi, N-Gain, ekspor SPSS/Excel, dan log sinkronisasi Google Sheets.
* **Unggah Poster**: Publikasi dan kelola poster edukatif Kaspro.

---

### 4. REST API Backend untuk Mobile App (Flutter)
Backend SIKERA dilengkapi dengan **REST API lengkap menggunakan Laravel Sanctum** yang siap dihubungkan dengan aplikasi mobile (Flutter / Dart):
* Autentikasi token Bearer (`/api/v1/auth/login`, `/register`, `/me`, `/biodata`)
* Dashboard terpadu mobile (`/api/v1/dashboard`)
* Modul, submateri, Pre-Test, dan Post-Test per modul (`/api/v1/modules/*`)
* Self-Care Tools & Period tracker (`/api/v1/selfcare/*`)
* Poster Kaspro download & share (`/api/v1/posters/*`)
* Studi Kasus & Forum tanya jawab (`/api/v1/cases`, `/api/v1/forum/*`)
* Gamifikasi Trivia, Mitos/Fakta, Diary (`/api/v1/gamification/*`)

> 📖 Dokumentasi lengkap endpoint API: **[`API_DOCUMENTATION.md`](API_DOCUMENTATION.md)**

---

## 🛠️ Tech Stack

* **Backend Framework**: Laravel 13 (PHP 8.3+)
* **API Authentication**: Laravel Sanctum (Bearer Token)
* **Database**: MySQL / MariaDB
* **Frontend Web**: Blade Templates, Tailwind CSS v4, Alpine.js, ApexCharts
* **UI Design Template**: TailAdmin Responsive Dashboard Theme
* **Asset Bundler**: Vite 6

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
* PHP >= 8.3 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`)
* Composer
* Node.js & NPM
* Server Database MySQL

### 2. Kloning & Pengaturan Lingkungan
```bash
# Kloning repositori
git clone https://github.com/username/sikera-web.git
cd sikera-web

# Salin konfigurasi environment
cp .env.example .env

# Pasang dependensi PHP
composer install

# Generate application key
php artisan key:generate
```

### 3. Konfigurasi Database
Buka file `.env` dan sesuaikan koneksi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikera_web
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeding Data Awal
```bash
# Jalankan migrasi dan isi data awal (users, modules, questions, posters, case studies)
php artisan migrate:fresh --seed
```

### 5. Kompilasi Aset Frontend
```bash
# Pasang dependensi Node.js
npm install

# Kompilasi aset dengan Vite
npm run build
```

### 6. Jalankan Server Lokal
```bash
# Jalankan web server Laravel
php artisan serve --port=8000
```
Buka browser di: **http://127.0.0.1:8000**

---

## 🔑 Kredensial Akun Pengujian (Demo)

| Peran (*Role*) | Alamat Email | Kata Sandi | Halaman Awal |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@sikera.id` | `password` | Dashboard Grafik Riset & Manajemen |
| **Dosen PA** | `dosen@unib.ac.id` | `password` | Dashboard Dosen PA & Evaluasi Kelas |
| **Mahasiswa Baru** | `mhs@unib.ac.id` | `password` | Menu Utama Mahasiswa |
| **Mahasiswa Sampel** | `sample1@unib.ac.id` | `password` | Menu Utama Mahasiswa (Data Evaluasi Lengkap) |

---

## 🧪 Menjalankan Pengujian Otomatis (*Test Suite*)

Proyek ini dilengkapi dengan unit dan feature tests untuk memvalidasi seluruh alur sistem web dan mobile API:
```bash
php artisan test
```

---

## 📄 Lisensi

Platform SIKERA dikembangkan di bawah lisensi [MIT License](LICENSE).
