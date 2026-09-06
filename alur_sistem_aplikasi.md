# Alur Sistem Aplikasi

Dokumentasi alur proses aplikasi berdasarkan diagram yang diberikan.

``` mermaid
flowchart TD
    A([Mulai: Buka Aplikasi]) --> B[Halaman Masuk]
    B --> C{Pilih Peran}

    %% =========================
    %% MAHASISWA
    %% =========================
    C -->|Mahasiswa| D{Sudah Isi}

    D -->|Belum| E[Isi Formulir Biodata:<br/>- Nama/Inisial<br/>- Usia<br/>- Jenis Kelamin<br/>- Agama<br/>- Program Studi<br/>- Pendidikan Terakhir]
    E --> F[Kerjakan Pre-Test]
    F --> G[(Tersimpan Otomatis ke Google Sheets<br/>Data Demografi + Skor Pre-Test)]
    G --> H[Menu Utama]

    D -->|Sudah| H

    H --> I1[Modul Edukasi & Video YouTube]
    H --> I2[Projek Visual Poster]
    H --> I3[Kalender Hadir, Macam Dara & IMT]
    H --> I4[Diskusi Anonim & Kasus Kebutuhan]

    H --> J[Pilih Keluar /]

    J --> K{Sudah Post-Test?}
    K -->|Belum| L[Kerjakan Post-Test]
    L --> M[(Update Nilai Akhir & Gain)]
    M --> N([Selesai / Logout])

    K -->|Sudah| N

    %% =========================
    %% DOSEN
    %% =========================
    C -->|Dosen| O[Dashboard Dosen]
    O --> P[Pantau Rata-rata Skor Mahasiswa]
    P --> Q([Selesai /])

    O --> R[Bahan Bimbingan & Konseling Kampus]
    R --> Q

    %% =========================
    %% SUPER ADMIN / PENGELOLA
    %% =========================
    C -->|Super Admin / Pengelola| S[Dashboard Super Admin / Pengelola]
    S --> T[Pantau Lembar Data Google Sheets]
    T --> U([Selesai /])

    S --> V[Unggah Modul & Poster Kaspro Baru]
    V --> U

    %% =========================
    %% KELOMPOK FITUR MAHASISWA
    %% =========================
    subgraph Fitur_Edukasi_dan_Pemantauan["Fitur Edukasi & Pemantauan"]
        H
        I1
        I2
        I3
        I4
    end
```

## Ringkasan Alur

### 1. Akses Awal

1.  Pengguna membuka aplikasi.
2.  Sistem menampilkan **Halaman Masuk**.
3.  Pengguna memilih salah satu peran:
    -   Mahasiswa
    -   Dosen
    -   Super Admin / Pengelola

### 2. Alur Mahasiswa

1.  Sistem memeriksa apakah data mahasiswa sudah diisi.
2.  Jika **belum**, mahasiswa mengisi formulir biodata:
    -   Nama/Inisial
    -   Usia
    -   Jenis Kelamin
    -   Agama
    -   Program Studi
    -   Pendidikan Terakhir
3.  Mahasiswa mengerjakan **Pre-Test**.
4.  Data demografi dan skor Pre-Test tersimpan otomatis ke **Google
    Sheets**.
5.  Jika data sudah pernah diisi, mahasiswa langsung menuju **Menu
    Utama**.
6.  Pada Menu Utama tersedia fitur:
    -   Modul Edukasi & Video YouTube
    -   Projek Visual Poster
    -   Kalender Hadir, Macam Dara & IMT
    -   Diskusi Anonim & Kasus Kebutuhan
7.  Mahasiswa memilih **Keluar**.
8.  Sistem memeriksa apakah mahasiswa sudah mengerjakan **Post-Test**.
9.  Jika belum, mahasiswa mengerjakan Post-Test.
10. Sistem memperbarui **Nilai Akhir & Gain**.
11. Proses berakhir dengan **Selesai / Logout**.

### 3. Alur Dosen

Setelah memilih peran **Dosen**, pengguna masuk ke **Dashboard Dosen**
yang menyediakan: - Pemantauan rata-rata skor mahasiswa. - Bahan
bimbingan & konseling kampus.

Kedua proses berakhir pada **Selesai**.

### 4. Alur Super Admin / Pengelola

Setelah memilih peran **Super Admin / Pengelola**, pengguna masuk ke
dashboard admin yang menyediakan: - Pemantauan lembar data Google
Sheets. - Unggah modul dan poster Kaspro baru.

Kedua proses berakhir pada **Selesai**.
