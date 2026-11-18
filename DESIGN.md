# DESIGN.md - Panduan Desain SIKERA

## 1. Identitas & Karakter Visual
* **Nama Produk:** SIKERA (Sistem Informasi Kesehatan Reproduksi Remaja Universitas Bengkulu)
* **Karakter:** Publikasi ilmiah kesehatan kampus yang hangat, empatik, terpercaya, tanpa vulgaritas, dan membumi bagi mahasiswa baru.
* **Design Read:** Halaman publikasi edukasi ilmiah dan pintu masuk intervensi riset kesehatan reproduksi bagi mahasiswa serta sivitas akademika Universitas Bengkulu, dalam bahasa visual Warm Editorial Botanical, dial `ENERGY 2 / RHYTHM 3 / MOTION 1`.
* **Dial:** `ENERGY 2 / RHYTHM 3 / MOTION 1`
  - **ENERGY 2 (Balanced Editorial):** Kontras tipografi serif editorial dan bidang warna hijau pinus memberi wibawa akademik yang hangat tanpa dekorasi berlebihan.
  - **RHYTHM 3 (Asymmetric & Mixed Compositions):** Setiap bagian berganti komposisi sesuai struktur kontennya: masthead asimetris dengan lembar protokol N-Gain, penjelajah silabus tab vertikal, pita referensi klinis FIGO berdampingan dengan bedah kasus, lembaran unggulan poster Kaspro, dan direktori rujukan.
  - **MOTION 1 (Functional States Only):** Hanya perubahan status hover, fokus keyboard yang tegas, dan perpindahan tab instan yang menghormati `prefers-reduced-motion`.

## 2. Palet Warna (3 Core + 1 Accent, Teruji Kontras WCAG AA)
* **Core 1 - Warm Paper & Espresso Ink (Netral Kertas Jurnal):**
  - Light mode: latar halaman `#FAF8F4` (`paper`), bidang sekunder `#F2EFE9` (`parchment`), kartu permukaan `#FFFFFF`, garis batas `#D6D0C4` (`rule`), batas kontrol interaktif `#78716C` (kontras >= 4.5:1), teks utama `#1C1917` (`ink`, 16.49:1), teks isi `#44403C` (`ink-body`, 9.68:1).
  - Dark mode: latar halaman `#151412` (`night`), permukaan kartu `#201E1B` (`night-surface`), permukaan angkat `#292622` (`night-raised`), garis batas `#3D3832` (`night-rule`), batas kontrol interaktif `#8C857B` (kontras >= 4.5:1), teks utama `#F5F2EB` (`night-ink`, 16.47:1), teks isi `#D6D0C4` (`night-body`, 10.83:1).
* **Core 2 - Botanical Pine (Identitas Akademik & Kesehatan):**
  - Light mode: `#1B4332` (`pine-700`, 10.44:1 di atas kertas; 11.08:1 dengan teks putih), pita referensi klinis `#143326` (`pine-900`).
  - Dark mode: `#52B788` (`pine-400` untuk tombol utama dengan teks gelap `#0F241B`, 6.59:1) dan `#74C69D` (`pine-300` untuk tautan/label di atas permukaan gelap, 8.16:1).
* **Core 3 - Terracotta Clay (Penanda Klinis, Reproduksi, & Rujukan):**
  - Light mode: `#9E3620` (`clay-600`, 6.59:1 di atas kertas) dan `#8C2D19` di atas latar `#FDECE8` (7.32:1).
  - Dark mode: `#F4A28C` (`clay-300`, 8.23:1 di atas permukaan gelap) dan `#F8C2B4` di atas `#3B1A14` (9.95:1).
* **Accent (1 Aksen Terukur) - Ochre Sand:**
  - Digunakan khusus pada penanda rumus riset N-Gain dan indikator observasi klinis: `#854D0E` di atas `#FEF7E6` (6.41:1) pada mode terang, serta `#FCD34D` di atas `#143326` / `#362409` (>= 9.51:1) pada permukaan gelap.

## 3. Tipografi & Motif Identitas
* **Display & Numerals (`Newsreader`):** Serif editorial humanis untuk tajuk utama (`h1`, `h2`) dan nomor indeks modul agar halaman terasa seperti jurnal kesehatan universitas yang kredibel.
* **UI & Body (`Plus Jakarta Sans`):** Sans-serif geometris-humanis untuk keterbacaan paragraf ilmiah, tabel klasifikasi klinis, label formulir, dan kontrol navigasi di layar kecil maupun lebar.
* **Motif Identitas (Editorial Index Bar):** Garis indeks dwiwarna proporsional (Botanical Pine + Terracotta Clay) disertai penomoran seri dua digit (`01` hingga `04`) yang berulang konsisten pada masthead, lembar protokol, dan kartu silabus.

## 4. Spasi, Radius, & Elevasi
* **Border Radius:** `rounded-xl` (12px) untuk lembaran utama dan panel silabus, `rounded-lg` (8px) untuk tombol serta tab interaktif, `rounded-sm` (2px) untuk indikator warna klinis. Bebas dari bentuk kapsul (pill) seragam.
* **Elevasi:** `shadow-sm` khusus pada lembaran fokus utama (protokol riset dan panel isi silabus) untuk memisahkan hierarki dari latar kertas; elemen pendukung menggunakan garis batas tegas tanpa bayangan mengambang atau efek pendar (glow).
* **Responsivitas & Aksesibilitas:** Target sentuh minimal 44x44px (`min-h-11`), skala tipografi cair lintas ukuran layar (ponsel, tablet, desktop), indikator `:focus-visible` kontras tinggi di kedua tema, serta penghormatan penuh terhadap `prefers-reduced-motion`.
