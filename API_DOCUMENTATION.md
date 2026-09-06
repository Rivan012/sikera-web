# Dokumentasi REST API SIKERA (Mobile Flutter Version)

Base URL: `http://localhost:8000/api/v1` atau `http://10.0.2.2:8000/api/v1` (Android Emulator)

Semua request yang memerlukan autentikasi wajib menyertakan header:
```http
Authorization: Bearer <TOKEN>
Accept: application/json
Content-Type: application/json
```

---

## 1. Autentikasi & Profil (`/api/v1/auth`)

### 1.1. Registrasi Mahasiswa Baru
- **Method & URL**: `POST /api/v1/auth/register`
- **Request Body**:
  ```json
  {
    "name": "Aisyah Putri",
    "initials": "AP",
    "usia": 18,
    "gender": "P",
    "agama": "Islam",
    "prodi": "Pendidikan Bahasa Inggris",
    "fakultas": "Fakultas Keguruan dan Ilmu Pendidikan (FKIP)",
    "pendidikan_terakhir": "SMA/SMK/MA/Sederajat",
    "nim": "A1D026045",
    "email": "aisyah@unib.ac.id",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response `201 Created`**:
  ```json
  {
    "success": true,
    "message": "Pendaftaran akun mahasiswa berhasil!",
    "data": {
      "token": "1|abcdef123456...",
      "token_type": "Bearer",
      "user": { ... }
    }
  }
  ```

### 1.2. Login
- **Method & URL**: `POST /api/v1/auth/login`
- **Request Body**:
  ```json
  {
    "email": "mhs@unib.ac.id",
    "password": "password"
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Login berhasil!",
    "data": {
      "token": "2|ghijk789012...",
      "token_type": "Bearer",
      "user": { ... }
    }
  }
  ```

### 1.3. Profil User Saat Ini
- **Method & URL**: `GET /api/v1/auth/me` *(Auth Required)*
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "user": {
        "id": 1,
        "name": "Aisyah Putri Maharani",
        "email": "mhs@unib.ac.id",
        "gender": "P",
        "points": 50,
        "streak_days": 3
      }
    }
  }
  ```

### 1.4. Logout
- **Method & URL**: `POST /api/v1/auth/logout` *(Auth Required)*

---

## 2. Beranda & Menu Utama (`/api/v1/dashboard`)

- **Method & URL**: `GET /api/v1/dashboard` *(Auth Required)*
- **Deskripsi**: Mengambil seluruh ringkasan data beranda (profil ringkas, status 4 modul, poster unggulan, data haid/IMT terakhir).
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "user": { "id": 1, "name": "Aisyah", "points": 50, "streak_days": 3 },
      "modules": [
        {
          "id": 1,
          "module_number": 1,
          "title": "Anatomi, Fisiologi, & Higienitas Reproduksi",
          "banner_image": "http://localhost:8000/images/banners/banner-module-1.svg",
          "total_topics": 4,
          "completed_topics": 2,
          "progress_percentage": 50,
          "has_pretest": true,
          "has_posttest": false
        }
      ],
      "posters": [ ... ],
      "selfcare": {
        "is_female": true,
        "latest_period": { ... },
        "latest_bmi": { ... }
      }
    }
  }
  ```

---

## 3. Modul Edukasi & Pre-Test / Post-Test (`/api/v1/modules`)

### 3.1. Daftar Semua Modul
- **Method & URL**: `GET /api/v1/modules` *(Auth Required)*

### 3.2. Detail Modul & Daftar Submateri
- **Method & URL**: `GET /api/v1/modules/{id}` *(Auth Required)*

### 3.3. Ambil Soal Pre-Test Modul
- **Method & URL**: `GET /api/v1/modules/{id}/pretest` *(Auth Required)*
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "module_id": 1,
      "module_number": 1,
      "questions": [
        {
          "id": 1,
          "question_text": "Arah yang benar saat membersihkan...",
          "options": [
            { "label": "A", "text": "..." },
            { "label": "B", "text": "..." }
          ]
        }
      ]
    }
  }
  ```

### 3.4. Submit Pre-Test Modul (Membuka Akses Materi)
- **Method & URL**: `POST /api/v1/modules/{id}/pretest` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "answers": {
      "1": "B",
      "2": "B"
    }
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Pre-Test Modul 1 selesai! Akses materi telah dibuka.",
    "data": {
      "score": 100,
      "points_earned": 25
    }
  }
  ```

### 3.5. Baca Submateri (HTML & YouTube Video)
- **Method & URL**: `GET /api/v1/modules/{moduleId}/topic/{topicId}` *(Auth Required)*
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "topic": {
        "id": 1,
        "topic_code": "1.1",
        "title": "Pengenalan Anatomi Organ Reproduksi Pria & Wanita",
        "youtube_video_id": "v3F4QW_h32M",
        "youtube_embed_url": "https://www.youtube.com/embed/v3F4QW_h32M",
        "content_html": "<p>Sistem reproduksi manusia...</p>"
      },
      "points_earned": 10,
      "ready_for_posttest": false
    }
  }
  ```

### 3.6. Ambil Soal Post-Test Modul
- **Method & URL**: `GET /api/v1/modules/{id}/posttest` *(Auth Required)*

### 3.7. Submit Post-Test Modul (Hitung N-Gain)
- **Method & URL**: `POST /api/v1/modules/{id}/posttest` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "answers": {
      "1": "B",
      "2": "B"
    }
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "pretest_score": 60,
      "posttest_score": 100,
      "n_gain_score": 1.0,
      "effectiveness_category": "Tinggi",
      "points_earned": 50
    }
  }
  ```

---

## 4. Self-Care Tools (`/api/v1/selfcare`)

### 4.1. Ringkasan & Prediksi Siklus Haid + IMT
- **Method & URL**: `GET /api/v1/selfcare/summary` *(Auth Required)*

### 4.2. Simpan Catatan Siklus Haid (Khusus Perempuan)
- **Method & URL**: `POST /api/v1/selfcare/period` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "start_date": "2026-09-01",
    "end_date": "2026-09-06",
    "cycle_length": 28,
    "period_duration": 5,
    "flow_level": "sedang",
    "flow_color": "Merah Terang",
    "nrs_pain_score": 3,
    "symptoms": ["Kram Perut", "Lelah"],
    "notes": "Keluhan ringan hari pertama."
  }
  ```

### 4.3. Hitung & Simpan IMT
- **Method & URL**: `POST /api/v1/selfcare/bmi` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "weight_kg": 54.0,
    "height_cm": 162.0
  }
  ```

### 4.4. Panduan Visual Warna Darah & Higienitas
- `GET /api/v1/selfcare/guides/blood`
- `GET /api/v1/selfcare/guides/hygiene`

---

## 5. Projek Visual Poster Kaspro (`/api/v1/posters`)

- `GET /api/v1/posters` &rarr; Daftar seluruh poster Kaspro.
- `POST /api/v1/posters/{id}/download` &rarr; Catat unduhan poster.
- `POST /api/v1/posters/{id}/share` &rarr; Catat share poster ke media sosial.

---

## 6. Studi Kasus & Forum Anonim (`/api/v1/cases` & `/api/v1/forum`)

- `GET /api/v1/cases` &rarr; Daftar studi kasus nyata indekos/kampus (hukum UU TPKS & medis).
- `GET /api/v1/cases/{id}` &rarr; Detail studi kasus.
- `GET /api/v1/forum` &rarr; Daftar pertanyaan forum tanya jawab.
- `POST /api/v1/forum` &rarr; Tulis pertanyaan anonim baru (`title`, `topic`, `content`).
- `GET /api/v1/forum/{id}` &rarr; Detail pertanyaan & daftar balasan konselor.
- `POST /api/v1/forum/{id}/reply` &rarr; Kirim balasan (`reply_content`).

---

## 7. Gamifikasi Harian (`/api/v1/gamification`)

- `GET /api/v1/gamification/trivia` &rarr; Ambil kuis trivia harian.
- `POST /api/v1/gamification/trivia` &rarr; Kirim jawaban trivia (`question_id`, `answer`).
- `GET /api/v1/gamification/myth-fact` &rarr; Ambil kartu Mitos vs Fakta harian.
- `POST /api/v1/gamification/myth-fact` &rarr; Kirim tebakan (`card_id`, `answer`: `"fact"` / `"myth"`).
- `GET /api/v1/gamification/diary` &rarr; Daftar riwayat catatan mood.
- `POST /api/v1/gamification/diary` &rarr; Simpan catatan mood (`mood`, `encrypted_note`, `entry_date`).
