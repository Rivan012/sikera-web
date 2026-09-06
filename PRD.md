# Product Requirement Document (PRD)
# SIKERA (Sistem Informasi Kesehatan Reproduksi Remaja)

---

## 1. Document Overview
* **Product Name:** SIKERA (Sistem Informasi Kesehatan Reproduksi Remaja)
* **Document Version:** 1.0.0
* **Target Users:** Mahasiswa Baru Universitas di Bengkulu, Dosen Pembimbing Akademik (PA), Tim Peneliti / Super Admin
* **Document Status:** Draft

---

## 2. Executive Summary & Product Vision
**SIKERA** adalah aplikasi edukasi kesehatan reproduksi berbasis *mobile* yang dirancang sebagai media pembelajaran interaktif sekaligus instrumen evaluasi kognitif dan intervensi riset kesehatan bagi kalangan remaja dan mahasiswa di lingkungan kampus.

Aplikasi ini menggabungkan materi ilmiah komprehensif, fitur pelacak kesehatan mandiri (*self-care tracker*), rubrik dinamika sosial kampus, sarana perlindungan/darurat kekerasan seksual, serta gamifikasi harian untuk meningkatkan literasi dan retensi pengguna. SIKERA juga terintegrasi langsung dengan instrumen evaluasi riset (*Pre-Test* dan *Post-Test*) dengan mekanisme *gating* terstruktur untuk mengukur efektivitas intervensi edukasi secara terukur (*N-Gain score*).

---

## 3. Product Goals & Objectives
1. **Peningkatan Literasi Kesehatan Reproduksi:** Menyediakan materi edukasi yang valid, ilmiah, non-vulgar, dan relevan dengan realitas kehidupan mahasiswa.
2. **Pengukuran Efektivitas Edukasi (Riset):** Mengukur peningkatan pemahaman kognitif responden melalui evaluasi *pre-test* dan *post-test* yang terintegrasi secara otomatis ke *database* analisis (Google Sheets / SPSS).
3. **Penyediaan Sarana Pemantauan & Perlindungan Mandiri:** Memberikan *tool* pelacak siklus menstruasi, nyeri haid (NRS), IMT, serta akses langsung ke saluran darurat/konseling kampus (Satgas PPKS & Hotline Faskes).
4. **Pendampingan Akademik Terpadu:** Memfasilitasi Dosen Pembimbing Akademik (PA) dalam memantau tren literasi dan memberikan rujukan konseling tanpa melanggar privasi mahasiswa.

---

## 4. User Personas & Role-Based Access Control (RBAC)

| Peran (*Role*) | Deskripsi | Akses Utama |
| :--- | :--- | :--- |
| **User (Mahasiswa)** | Mahasiswa aktif / responden penelitian maba UNIB. | • Mengisi Pre-Test (terkunci di awal)<br>• Mengakses seluruh modul materi & video<br>• Menggunakan *tool* mandiri (Kalender haid, NRS, IMT, Diary)<br>• Berpartisipasi di Anonymous Forum & Gamifikasi<br>• Mengisi Post-Test (*exit-gating*) |
| **Dosen PA (Pembimbing Akademik)** | Dosen pembimbing akademik di tingkat fakultas/prodi. | • Dashboard statistik agregat kelas (*N-Gain*, persentase partisipasi)<br>• Katalog modul & poster untuk materi bimbingan<br>• Merespons pertanyaan konseling anonim mahasiswa |
| **Super Admin (Tim Peneliti)** | Pengelola sistem, peneliti, dan administrator konten. | • Manajemen CMS (unggah modul, poster KesproFeed, video YouTube, hotline)<br>• Monitoring data sinkronisasi Google Sheets & ekspor SPSS/Excel<br>• Manajemen akun & reset akses responden |

---

## 5. Functional Requirements & Feature Breakdown

### 5.1. Research Gating & Evaluation System
* **FR-RES-01: One-Time Pre-Test Gating:**
  * Saat pertama kali akun mahasiswa dibuat/login, seluruh menu utama, modul, dan *tracker* terkunci.
  * Mahasiswa wajib menyelesaikan kuesioner pre-test hingga *submit*.
  * Status `pretest_completed = true` membuka akses ke seluruh fitur aplikasi.
* **FR-RES-02: Exit Post-Test Gating:**
  * Ketika pengguna menekan tombol *Logout* atau *Keluar Aplikasi*, sistem memicu *modal pop-up* kuesioner post-test.
  * Sesi keluar hanya berhasil dan status tersimpan jika seluruh instrumen post-test telah dijawab dan terkirim ke server.
* **FR-RES-03: Real-Time Data Pipeline:**
  * Sinkronisasi data jawaban *Pre-Test* dan *Post-Test* secara otomatis ke Google Sheets via Google Apps Script API / Webhook.
  * Kemampuan ekspor data mentah berformat `.xlsx` dan `.csv` siap olah untuk analisis SPSS.

---

### 5.2. Pemantauan Fisik & Kesehatan Mandiri (*Self-Care Tools*)
* **FR-PHY-01: Period & Ovulation Tracker:** Kalender siklus menstruasi interaktif untuk mencatat hari pertama haid, estimasi durasi siklus, serta prediksi masa subur dan ovulasi berikutnya.
* **FR-PHY-02: Panduan Visual Karakteristik Darah Haid:** Antarmuka visual yang menampilkan spektrum warna (merah terang, cokelat, merah muda, kehitaman) dan tekstur darah haid, disertai anotasi medis kapan kondisi tersebut fisiologis vs patologis.
* **FR-PHY-03: Dismenore Scale (Numeric Rating Scale / NRS):** Skala ukur mandiri derajat nyeri haid (skor 0–10) dengan algoritma rekomendasi tindakan (kompres hangat, teknik relaksasi napas, hingga rekomendasi rujukan faskes bila skor tinggi).
* **FR-PHY-04: Kalkulator Indeks Massa Tubuh (IMT):** Perhitungan otomatis status gizi berdasarkan tinggi (cm) dan berat badan (kg) untuk deteksi risiko KEK (Kekurangan Energi Kronis) atau obesitas yang mempengaruhi keteraturan hormon reproduksi.
* **FR-PHY-05: Tips Perawatan Organ Kewanitaan:** Panduan higienitas genitalia harian (kebersihan arah depan-ke-belakang, pemilihan pakaian dalam, mitigasi keputihan abnormal).

---

### 5.3. Edukasi Visual & Konten Dinamis
* **FR-EDU-01: Modul Multimedia Terpadu:** Penyajian materi terstruktur per topik yang menggabungkan teks ilmiah ringkas, infografis beresolusi tinggi, dan *embedded player* video edukasi YouTube.
* **FR-EDU-02: KesproFeed (Pojok Visual Kespro):** Galeri poster/pamflet edukatif digital bergaya feed visual interaktif, dilengkapi fitur:
  * Unduh poster kualitas HD ke galeri lokal (*Download to gallery*).
  * Tombol berbagi langsung (*One-click share*) ke WhatsApp Status dan Instagram Story.
* **FR-EDU-03: Downloadable Self-Care Guidebook:** Menu unduh dokumen PDF buku saku kesehatan reproduksi untuk akses luring (*offline reading*).

---

### 5.4. Relasi, Sosial, & Perlindungan Kampus
* **FR-SOC-01: Studi Kasus Nyata Mahasiswa (*College Case Studies*):** Rubrik naratif kasus riil kehidupan kampus/indekos (misal: *living together*, manipulasi relasi/*gaslighting*, risiko IMS, KTD) beserta analisis hukum, etika, dan mitigasi medis.
* **FR-SOC-02: Relationship Risk Checker:** Kuesioner skrining mandiri batasan relasi pacaran dengan output klasifikasi tingkat risiko: *Safe*, *Warning*, atau *High Risk*.
* **FR-SOC-03: Anonymous Chat (Forum Tanya Jawab Anonim):** Forum diskusi dua arah terenkripsi di mana identitas mahasiswa disamarkan, memungkinkan tanya jawab bebas stigma seputar isu kesehatan reproduksi bersama konselor/dosen PA.
* **FR-SOC-04: Direct Hotline & Tombol Darurat:** Pintasan panggilan cepat (*quick dial / direct link*) ke:
  * Satgas Pencegahan dan Penanganan Kekerasan Seksual (PPKS) Universitas Bengkulu.
  * Unit Layanan Konseling Mahasiswa.
  * Fasilitas Kesehatan & Rumah Sakit Rujukan terdekat di Kota Bengkulu.
* **FR-SOC-05: Dashboard Dosen PA:** Panel web/mobile khusus Dosen PA untuk melihat rekapitulasi data agregat kelas (persentase partisipasi mahasiswa bimbingan & peningkatan rata-rata *N-Gain* tanpa membuka data privasi individu).

---

### 5.5. Gamifikasi & Retensi Pengguna (*Daily Retention*)
* **FR-GAM-01: Daily Streak & Health Trivia:** Kuis kilat 1 pertanyaan harian (durasi 60 detik) untuk menjaga *daily active usage*. Pengguna yang konsisten mendapatkan poin dan *badges* penghargaan profil.
* **FR-GAM-02: Myth vs Fact Card Swiper:** Antarmuka kartu geser interaktif (*swipe right* untuk Fakta, *swipe left* untuk Mitos) guna meluruskan miskonsepsi seputar seksualitas dan reproduksi.
* **FR-GAM-03: Secret Diary & Mood Log:** Jurnal harian keluhan fisik dan suasana hati (*mood tracker*) yang dilindungi enkripsi lokal dan autentikasi biometrik / PIN HP.

---

## 6. Silabus & Struktur Modul Pembelajaran

```
SIKERA Learning Syllabus
│
├── Modul 1: Anatomi, Fisiologi, & Higienitas Reproduksi
│   ├── Materi 1.1: Pengenalan Anatomi Organ Reproduksi Pria & Wanita
│   ├── Materi 1.2: Pubertas & Mekanisme Hormonal (Mimpi Basah & Siklus Haid)
│   ├── Materi 1.3: Variasi Karakteristik Darah Menstruasi
│   └── Materi 1.4: Higienitas Genitalia & Penanganan Dismenore (Skala NRS)
│
├── Modul 2: Batasan Pergaulan, Pacaran Sehat, & Dinamika Kampus
│   ├── Materi 2.1: Batasan Fisik & Emosional (Healthy Dating vs Relasi Toksik)
│   ├── Materi 2.2: Bedah Kasus Lingkungan Mahasiswa (Living Together & Kos-kosan)
│   └── Materi 2.3: Keterampilan Asertif Remaja (Teknik Berkata "TIDAK")
│
├── Modul 3: Risiko Medis Seks Bebas & Perlindungan Diri
│   ├── Materi 3.1: Infeksi Menular Seksual (IMS) & HIV/AIDS
│   ├── Materi 3.2: Kehamilan Tidak Diinginkan (KTD) & Bahaya Aborsi Tidak Aman
│   └── Materi 3.3: Keamanan Digital Remaja (Cybersexuality, Sexting, & KBGO)
│
└── Modul 4: Mitigasi Pernikahan Dini & Kesiapan Berkeluarga
    ├── Materi 4.1: Empat Pilar Kesiapan Menikah (BKKBN: Usia 21 Wanita & 25 Pria)
    ├── Materi 4.2: Dampak Medis Kehamilan Usia Dini (CPD, Preeklamsia, Kematian Maternal)
    └── Materi 4.3: Pencegahan Stunting sejak Usia Remaja (Gizi & Anemia)
```

---

## 7. Metodologi Riset & Distribusi Sampel Penelitian

### 7.1. Formula Penentuan Sampel
* **Populasi Awal Mahasiswa Baru UNIB (D3 & S1):** 5.516 mahasiswa
* **Eksklusi Fakultas Kedokteran dan Ilmu Kesehatan (FKIK):** 312 mahasiswa
* **Populasi Efektif ($N$):** $5.516 - 312 = 5.204$ mahasiswa
* **Tingkat Presisi / Margin of Error ($e$):** 10% ($0,1$)
* **Perhitungan Rumus Slovin:**
  $$n = \frac{N}{1 + N(e)^2} = \frac{5.204}{1 + 5.204(0,1)^2} = \frac{5.204}{53,04} \approx 98,11 \rightarrow 98 \text{ responden}$$
* **Antisipasi Dropout (10%):** $10\% \times 98 = 9,8 \approx 10 \text{ responden}$
* **Total Target Sampel ($n_{\text{total}}$):** **108 responden**

### 7.2. Rumus Alokasi Sampel Proporsional per Prodi
$$\text{Sampel per Prodi} = \left(\frac{N_i}{N}\right) \times n_{\text{total}} = \left(\frac{N_i}{5.204}\right) \times 108$$

### 7.3. Rekapitulasi Alokasi Sampel per Fakultas

| No | Fakultas | Total Populasi Maba ($N$) | Kebutuhan Sampel Responden |
| :---: | :--- | :---: | :---: |
| 1 | Fakultas Keguruan dan Ilmu Pendidikan (FKIP) | 1.080 | 22 |
| 2 | Fakultas Hukum (FH) | 437 | 9 |
| 3 | Fakultas Ekonomi dan Bisnis (FEB) | 599 | 12 |
| 4 | Fakultas Ilmu Sosial dan Ilmu Politik (FISIP) | 553 | 12 |
| 5 | Fakultas Pertanian (FP) | 810 | 18 |
| 6 | Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA) | 577 | 13 |
| 7 | Fakultas Teknik (FT) | 544 | 11 |
| 8 | Fakultas Kedokteran dan Ilmu Kesehatan (FKIK) | 0 *(dieksklusi)* | 0 |
| **TOTAL** | **Universitas Bengkulu** | **5.204** | **108** |

---

## 8. Arsitektur Teknis & Spesifikasi Sistem

### 8.1. Tech Stack Rekomendasi
* **Mobile Frontend:** Flutter (Dart) atau React Native / Progressive Web App (PWA) responsif.
* **Backend & Database:** Node.js (Express / NestJS) atau Firebase / Supabase.
* **Database Relasional & Dokumen:** PostgreSQL / Firestore (menyimpan data user, modul, logs kuis).
* **Research Integration Layer:** Google Apps Script Webhook API untuk sinkronisasi instan ke spreadsheet penelitian.
* **Media & Asset Storage:** Cloudinary / Supabase Storage / Firebase Storage (poster KesproFeed & PDF Guidebook).

### 8.2. Entity Relationship Diagram (High-Level Data Model)
* **Users:** `id`, `nim`, `email`, `role`, `fakultas`, `prodi`, `created_at`, `pretest_status`, `posttest_status`.
* **PreTestResponses:** `id`, `user_id`, `scores_per_module`, `raw_answers`, `total_score`, `submitted_at`.
* **PostTestResponses:** `id`, `user_id`, `scores_per_module`, `raw_answers`, `total_score`, `submitted_at`, `n_gain_score`.
* **PeriodLogs:** `id`, `user_id`, `start_date`, `end_date`, `cycle_length`, `flow_color`, `nrs_pain_level`.
* **DiaryEntries:** `id`, `user_id`, `encrypted_content`, `mood`, `created_at`.
* **ForumPosts & Comments:** `id`, `author_anon_id`, `role`, `content`, `status`, `created_at`.
* **EducationalModules:** `id`, `module_number`, `title`, `description`, `content_md`, `youtube_video_id`, `order`.

---

## 9. Non-Functional Requirements

1. **Keamanan & Privasi Data (Data Privacy):**
   * Data personal dan jawaban kuesioner sensitif harus dipisahkan dari identitas publik (*anonymized data tagging*).
   * Catatan *Secret Diary* wajib terenkripsi di sisi klien (*client-side encryption*) dan didukung proteksi biometrik/PIN lokal.
2. **Kinerja & Responsivitas (Performance):**
   * Waktu muat halaman (*screen render time*) < 1.5 detik pada jaringan seluler 4G.
   * Modul media dan infografis dioptimalkan (*compressed WebP/SVG*) untuk hemat kuota.
3. **Ketersediaan & Aksesibilitas (Offline Capability):**
   * Modul buku saku PDF dan data kalender siklus menstruasi lokal tetap dapat diakses tanpa koneksi internet (*offline caching*).
4. **Validasi & Integritas Data Riset:**
   * Setiap pengiriman jawaban kuesioner memiliki mekanisme validasi form lengkap (mencegah *missing fields*) dan penanda waktu (*timestamping*) yang presisi.

---

## 10. Roadmap & Timeline Pengembangan

* **Fase 1: Requirement, System Design & UI/UX Wireframing** (Minggu 1-2)
  * Finalisasi PRD, skema basis data, dan desain prototipe Figma.
* **Fase 2: Core Development & Research Gating Engine** (Minggu 3-5)
  * Pembuatan sistem autentikasi, gating Pre-Test/Post-Test, modul materi, dan webhook Google Sheets.
* **Fase 3: Self-Care Tools, Gamification, & Role Dashboards** (Minggu 6-8)
  * Implementasi pelacak siklus haid, kalkulator IMT/NRS, KesproFeed, dan Dashboard Dosen PA.
* **Fase 4: Testing, Security Audit & Pilot Run** (Minggu 9-10)
  * Pengujian fungsionalitas, validasi instrumen riset pada 108 responden sampel UNIB, dan perbaikan bug.
* **Fase 5: Deployment & Full Research Rollout** (Minggu 11)
  * Rilis aplikasi ke responden dan pemantauan data evaluasi *N-Gain*.
