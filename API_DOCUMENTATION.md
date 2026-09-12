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

## 4. Self-Care Tools & Perkiraan Siklus Menstruasi (`/api/v1/selfcare`)

Perhitungan perkiraan siklus menstruasi pada SIKERA:
1. **Perkiraan Hari ke-1 Haid Berikutnya**: `LMP + Rata-rata Panjang Siklus` (rata-rata bergerak 3-6 siklus terakhir).
2. **Estimasi Hari Ovulasi**: `Estimasi Hari ke-1 Haid Berikutnya - 14 Hari` (fase luteal 14 hari sebelum haid).
3. **Jendela Masa Subur 6 Hari**: `(Hari Ovulasi - 5 Hari) s.d. (Hari Ovulasi + 1 Hari)` (periode peluang pembuahan optimal).
4. **Variasi Siklus**: Awal = `Siklus Terpendek - 18`, Akhir = `Siklus Terpanjang - 11`.

---

### 4.1. Ringkasan & Prediksi Siklus Haid + IMT
- **Method & URL**: `GET /api/v1/selfcare/summary` *(Auth Required)*
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Ringkasan data self-care berhasil dimuat.",
    "data": {
      "is_female": true,
      "period_tracker": {
        "available": true,
        "latest_period": {
          "id": 1,
          "start_date": "2026-09-01",
          "end_date": "2026-09-06",
          "cycle_length": 28,
          "period_duration": 5,
          "flow_level": "sedang",
          "flow_color": "Merah Terang",
          "nrs_pain_score": 3,
          "symptoms": ["Kram Perut"]
        },
        "predictions": {
          "next_period_date": "2026-09-29",
          "ovulation_date": "2026-09-15",
          "fertile_window_start": "2026-09-10",
          "fertile_window_end": "2026-09-16",
          "countdown_text": "17 hari lagi",
          "summary_narrative": "Haid berikutnya diperkirakan tiba pada tanggal 29 September 2026 (sekitar 17 hari lagi).",
          "details": { ... },
          "current_status": {
            "cycle_day": 12,
            "phase_name": "Masa Subur",
            "fertility_status": "Tinggi"
          }
        }
      },
      "bmi_tracker": { ... }
    }
  }
  ```

### 4.2. Prediksi Siklus Menstruasi Pengguna Terdaftar (Khusus Perempuan)
- **Method & URL**: `GET /api/v1/selfcare/period/prediction` *(Auth Required)*
- **Deskripsi**: Mengambil perkiraan siklus haid, narasi ringkas yang mudah dipahami, masa subur, ovulasi, status fase siklus saat ini, dan proyeksi siklus mendatang berdasarkan riwayat data pengguna.
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Prediksi siklus menstruasi dan masa subur pengguna berhasil dimuat.",
    "data": {
      "summary_narrative": "Haid berikutnya diperkirakan tiba pada tanggal 29 September 2026 (sekitar 17 hari lagi).",
      "countdown_text": "17 hari lagi",
      "cycle_metrics": {
        "average_cycle_length": 28,
        "shortest_cycle": 28,
        "longest_cycle": 28,
        "is_regular": true,
        "regularity_status": "Siklus Teratur",
        "cycle_category": "Normal (21 - 35 hari)"
      },
      "current_status": {
        "cycle_day": 12,
        "phase_name": "Masa Subur",
        "phase_description": "Periode waktu di mana sel telur bersiap dilepaskan dan peluang pembuahan optimal.",
        "days_until_next_period": 17,
        "countdown_text": "17 hari lagi",
        "summary_narrative": "Haid berikutnya diperkirakan tiba pada tanggal 29 September 2026 (sekitar 17 hari lagi).",
        "is_period_late": false
      },
      "predictions": {
        "next_period": {
          "start_date": "2026-09-29",
          "end_date": "2026-10-03",
          "countdown_text": "17 hari lagi",
          "narrative": "Haid berikutnya diperkirakan tiba pada tanggal 29 September 2026 (sekitar 17 hari lagi).",
          "formula_applied": "Dihitung dari haid terakhir (01/09/2026) + siklus 28 hari"
        },
        "ovulation": {
          "date": "2026-09-15",
          "narrative": "Pelepasan sel telur matang diperkirakan pada tanggal 15 September 2026.",
          "formula_applied": "14 hari sebelum perkiraan haid berikutnya (29/09/2026)"
        },
        "fertile_window": {
          "start_date": "2026-09-10",
          "end_date": "2026-09-16",
          "total_days": 6,
          "peak_date": "2026-09-15",
          "narrative": "Masa subur berlangsung selama 6 hari (10 Sep s.d. 16 Sep 2026) dengan peluang pembuahan paling tinggi.",
          "formula_applied": "Rentang 6 hari (5 hari sebelum puncak ovulasi hingga 1 hari setelahnya)"
        },
        "ogino_knaus": {
          "applicable": true,
          "cycle_day_start": 10,
          "cycle_day_end": 17,
          "narrative": "Dengan variasi siklusmu, masa subur diperkirakan berlangsung antara 11 Sep sampai 18 Sep 2026.",
          "formula_applied": "Disesuaikan dengan siklus terpendek dan terpanjang"
        }
      },
      "upcoming_cycles": [ ... ],
      "clinical_disclaimer": "Perkiraan siklus haid ini dihitung sebagai panduan pola tubuh mandiri agar kamu lebih siap setiap bulannya. Tanggal sebenarnya dapat maju atau mundur beberapa hari tergantung kondisi fisik dan pikiranmu."
    }
  }
  ```

### 4.3. Kalkulator & Prediksi Siklus Haid Ilmiah (On-Demand / Simulasi)
- **Method & URL**: `POST /api/v1/selfcare/period/calculate` *(Publik & Auth)*
- **Request Body**:
  ```json
  {
    "last_period_date": "2026-09-01",
    "cycle_length": 28,
    "period_duration": 5,
    "cycle_history": [26, 31, 28, 29],
    "target_date": "2026-09-12",
    "projection_count": 3
  }
  ```
- **Response `200 OK`**: Mengembalikan hasil kalkulasi lengkap (Metrik Siklus, Status Hari Ini, Prediksi ACOG, Ovulasi WHO, Fertile Window Wilcox 6 hari, Ogino-Knaus, serta proyeksi multi-siklus).

### 4.4. Kalkulator & Simulasi Skor Dismenore WaLIDD (Publik & Auth)
- **Method & URL**: `POST /api/v1/selfcare/period/walidd/calculate`
- **Deskripsi**: Menghitung skor multidimensional WaLIDD (*Working ability, Location, Intensity, Days of pain*) dan menentukan kategori klinis dismenore secara otomatis.
- **Request Body**:
  ```json
  {
    "working_ability": 1,
    "locations": ["perut_bawah", "pinggang"],
    "nrs_score": 5,
    "pain_days": 2
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Perhitungan skor WaLIDD dan kategori dismenore berhasil dihitung.",
    "data": {
      "working_ability_score": 1,
      "location_score": 2,
      "intensity_score": 2,
      "pain_days_score": 1,
      "total_score": 6,
      "max_score": 12,
      "category": "Dismenore Sedang",
      "nrs_score": 5,
      "nrs_category": "Nyeri Sedang",
      "pain_days": 2,
      "selected_locations": ["perut_bawah", "pinggang"],
      "interpretation": "Nyeri kram haid mulai mengganggu konsentrasi dan menurunkan efisiensi aktivitas harian.",
      "action_advice": "Istirahat yang cukup, aplikasikan kompres hangat secara teratur, kurangi konsumsi kafein berlebih, dan konsultasikan dengan tenaga medis jika keluhan mengganggu kegiatan perkuliahan.",
      "color": "orange",
      "formula": "W (1) + L (2) + I (2) + D (1) = 6/12"
    }
  }
  ```

### 4.5. Kalkulator & Simulasi Skor Visual PBAC & Volume FIGO (Publik & Auth)
- **Method & URL**: `POST /api/v1/selfcare/period/pbac/calculate`
- **Deskripsi**: Menghitung skor visual PBAC (*Pictorial Blood Loss Assessment Chart - Higham et al.*) untuk mengestimasi volume pengeluaran darah menstruasi per siklus dan mendeteksi risiko Menoragia / *Heavy Menstrual Bleeding* (HMB &ge; 80 ml).
- **Request Body**:
  ```json
  {
    "pads_light": 3,
    "pads_medium": 2,
    "pads_heavy": 5,
    "clots_small": 1,
    "clots_large": 2
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Perhitungan skor PBAC dan klasifikasi volume darah berhasil dihitung.",
    "data": {
      "total_score": 124,
      "is_hmb": true,
      "code": "menoragia",
      "category": "Menoragia / HMB (Heavy Menstrual Bleeding)",
      "estimated_volume": "> 80 ml (Perdarahan Berlebih)",
      "interpretation": "Skor PBAC ≥ 100 poin mengindikasikan volume darah haid berlebih / menoragia (≥ 80 ml) dengan sensitivitas dan spesifisitas klinis > 80%. Kondisi ini berisiko memicu anemia defisiensi besi.",
      "action_advice": "Waspadai gejala lemas, cepat lelah, atau pusing. Dianjurkan memeriksa kadar hemoglobin (Hb) dan berkonsultasi dengan dokter spesialis obstetri & ginekologi (Sp.OG).",
      "color": "error",
      "breakdown": {
        "pads": {
          "light": { "count": 3, "points_per_item": 1, "score": 3 },
          "medium": { "count": 2, "points_per_item": 5, "score": 10 },
          "heavy": { "count": 5, "points_per_item": 20, "score": 100 },
          "total_score": 113
        },
        "clots": {
          "small": { "count": 1, "points_per_item": 1, "score": 1 },
          "large": { "count": 2, "points_per_item": 5, "score": 10 },
          "total_score": 11
        }
      },
      "formula": "Pembalut (113) + Gumpalan (11) = 124 Poin PBAC"
    }
  }
  ```

### 4.6. Simpan Catatan Siklus Haid (5 Komponen Referensi - Khusus Perempuan)
- **Method & URL**: `POST /api/v1/selfcare/period` *(Auth Required)*
- **Deskripsi**: Mencatat siklus menstruasi komprehensif berdasarkan 5 komponen (Menarche, Waktu Siklus, Karakteristik Aliran/Volume FIGO & Skoring PBAC, Keteraturan Siklus, serta Evaluasi Dismenore & WaLIDD).
- **Request Body**:
  ```json
  {
    "menarche_age": 12,
    "start_date": "2026-09-01",
    "end_date": "2026-09-06",
    "cycle_length": 28,
    "period_duration": 6,
    "is_regular": true,
    "flow_level": "sangat_deras",
    "flow_color": "Merah Terang",
    "blood_consistency": "Bergumpal",
    "pbac_pads_light": 2,
    "pbac_pads_medium": 3,
    "pbac_pads_heavy": 5,
    "pbac_clots_small": 1,
    "pbac_clots_large": 1,
    "nrs_pain_score": 4,
    "has_dysmenorrhea": true,
    "walidd_working_ability": 1,
    "walidd_locations": ["perut_bawah", "pinggang"],
    "walidd_pain_days": 2,
    "symptoms": ["Kram Perut", "Lelah"],
    "notes": "Keluhan kram hari pertama sampai kedua."
  }
  ```
- **Response `201 Created`**:
  ```json
  {
    "success": true,
    "message": "Catatan siklus haid dan evaluasi dismenore berhasil disimpan (+10 poin).",
    "data": {
      "period_log": {
        "id": 1,
        "menarche_age": 12,
        "start_date": "2026-09-01",
        "end_date": "2026-09-06",
        "cycle_length": 28,
        "period_duration": 6,
        "is_regular": true,
        "flow_level": "sangat_deras",
        "flow_color": "Merah Terang",
        "blood_consistency": "Bergumpal",
        "volume_category": "menoragia",
        "pbac_score": 122,
        "pbac_details": { ... },
        "nrs_pain_score": 4,
        "has_dysmenorrhea": true,
        "walidd_total_score": 6,
        "walidd_category": "Dismenore Sedang",
        "walidd_interpretation": "Nyeri kram haid mulai mengganggu konsentrasi...",
        ...
      },
      "prediction": { ... },
      "points_earned": 10
    }
  }
  ```

### 4.7. Riwayat Siklus Haid
- **Method & URL**: `GET /api/v1/selfcare/period/history` *(Auth Required)*

### 4.8. Hitung & Simpan IMT
- **Method & URL**: `POST /api/v1/selfcare/bmi` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "weight_kg": 54.0,
    "height_cm": 162.0
  }
  ```

### 4.9. Panduan Visual Warna Darah & Higienitas
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
