# DESIGN.md - Panduan Desain SIKERA

## 1. Identitas & Karakter Visual
* **Nama Produk:** SIKERA (Sistem Informasi Kesehatan Reproduksi Remaja)
* **Karakter:** Edukatif, empatik, terpercaya, ilmiah tanpa vulgaritas, ramah mahasiswa.
* **Dial:** `ENERGY 2 / RHYTHM 2 / MOTION 1`
  - **ENERGY 2 (Balanced):** Memberikan kehangatan dan kejelasan visual yang ramah tanpa berlebihan.
  - **RHYTHM 2 (Consistent with clear section hierarchy):** Struktur modul dan tracker tersusun rapi dengan kartu metrik yang jelas.
  - **MOTION 1 (Subtle & Functional):** Transisi halaman dan interaksi tombol yang halus dan fungsional.

## 2. Palet Warna (Max 3 Core + 1 Accent)
* **Primary (Emerald Teal):** `#0f766e` (Teal 700) / `#0d9488` (Teal 600) — Melambangkan kesehatan, kesegaran, dan kenyamanan medis.
* **Secondary (Warm Coral/Rose):** `#e11d48` (Rose 600) / `#f43f5e` (Rose 500) — Aksen untuk tracking menstruasi, kesehatan reproduksi, dan urgensi hotline.
* **Neutral Background & Surface:** 
  - Light mode: `#f8fafc` (Slate 50) dengan surface `#ffffff` (White) dan border `#e2e8f0` (Slate 200).
  - Dark mode: `#0f172a` (Slate 900) dengan surface `#1e293b` (Slate 800) dan border `#334155` (Slate 700).
* **Accent (Amber Gold):** `#d97706` (Amber 600) — Untuk gamifikasi, daily streak, dan badge penghargaan.

## 3. Tipografi
* **Font Family:** `Plus Jakarta Sans` / `Inter`, sans-serif.
* **Hierarki:**
  - H1/Page Title: `text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white`
  - H2/Section Title: `text-lg sm:text-xl font-semibold text-slate-800 dark:text-slate-100`
  - Body Text: `text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed`
  - Caption/Labels: `text-xs font-medium text-slate-500 dark:text-slate-400`

## 4. Spacing & Radius
* **Border Radius:** `rounded-xl` (12px) untuk kartu, `rounded-lg` (8px) untuk tombol dan input kontrol. Tidak ada pill-shape berlebihan.
* **Shadows:** `shadow-sm` untuk kartu default, `shadow-md` untuk modal/overlay. Tidak menggunakan blur/glow berlebihan.
* **Whitespace:** Margins dan paddings konsisten berbasis kelipatan 4/8 (`p-4`, `p-6`, `gap-4`, `gap-6`).
