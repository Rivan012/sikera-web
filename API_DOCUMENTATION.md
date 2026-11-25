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
- Untuk login Google dari aplikasi, kirim `{"id_token":"<ID token Google>"}`. Setelah token terverifikasi (mendukung `GOOGLE_CLIENT_ID`, `GOOGLE_ANDROID_CLIENT_ID`, `GOOGLE_IOS_CLIENT_ID`, dan `GOOGLE_ALLOWED_CLIENT_IDS`), format respons sama dengan login email.
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
        "avatar": null,
        "gender": "P",
        "points": 50,
        "streak_days": 3
      }
    }
  }
  ```
  Properti `avatar` juga tersedia pada objek `user` di respons registrasi dan login. Nilainya berupa URL foto profil jika tersedia, atau `null`.

### 1.4. Logout
- **Method & URL**: `POST /api/v1/auth/logout` *(Auth Required)*

---

## 2. Beranda & Menu Utama (`/api/v1/dashboard`)

- **Method & URL**: `GET /api/v1/dashboard` *(Auth Required)*
- **Deskripsi**: Mengambil seluruh ringkasan data beranda (profil ringkas, status modul beserta Pre-Test per modul, status gerbang Post-Test terpadu 3 hari, poster unggulan, dan data haid/IMT terakhir).
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
          "banner_image": "http://localhost:8000/api/v1/modules/1/banner",
          "total_topics": 4,
          "completed_topics": 2,
          "progress_percentage": 50,
          "has_pretest": true,
          "has_posttest": false,
          "n_gain": null
        }
      ],
      "posttest_gate": {
        "state": "locked_incomplete",
        "total_modules": 4,
        "completed_modules": 0,
        "total_topics": 16,
        "completed_topics": 2,
        "all_modules_completed": false,
        "all_modules_completed_at": null,
        "posttest_available_at": null,
        "is_posttest_unlocked": false,
        "remaining_seconds": 0,
        "delay_days": 3,
        "has_completed_posttest": false,
        "latest_posttest": null
      },
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

## 3. Modul Edukasi, Flipbook PDF, & Evaluasi (`/api/v1/modules` & `/api/v1/posttest`)

Alur evaluasi SIKERA mengikuti protokol bertahap:
1. **Pre-Test per Modul**: Dikerjakan tepat sebelum membuka daftar submateri dan flipbook PDF modul terkait.
2. **Penyelesaian Seluruh Submateri**: Ketika seluruh subbab di semua modul aktif selesai dibaca, sistem mencatat timestamp `all_modules_completed_at`.
3. **Post-Test Terpadu (Jeda 3 Hari / 72 Jam)**: Satu Post-Test umum (`module_target = null`) terbuka otomatis pada `all_modules_completed_at + 3 hari`. Skor N-Gain dihitung dari perbandingan skor total rata-rata Pre-Test seluruh modul terhadap skor total Post-Test, dan token autentikasi tetap aktif setelah pengiriman.

### 3.1. Daftar Semua Modul
- **Method & URL**: `GET /api/v1/modules` *(Auth Required)*
- Mengembalikan array `modules` beserta objek `posttest_gate`.

### 3.2. Detail Modul & Daftar Submateri
- **Method & URL**: `GET /api/v1/modules/{id}` *(Auth Required)*
- Setiap item `topics` memuat `has_pdf`, `pdf_url` (`/api/v1/modules/{id}/topic/{topicId}/pdf`), `pdf_original_name`, `pdf_size`, `is_completed`, dan `is_locked`.

### Banner Modul (Publik)
- **Method & URL**: `GET /api/v1/modules/{id}/banner`
- **Deskripsi**: Mengambil banner modul dalam format file yang tersimpan. Format PNG, JPG/JPEG, dan SVG didukung; banner otomatis yang dibuat sistem menggunakan SVG. Respons mengirimkan `Content-Type` sesuai format gambar.

### 3.3. Ambil Soal Pre-Test Modul
- **Method & URL**: `GET /api/v1/modules/{id}/pretest` *(Auth Required)*
- **Deskripsi**: Mengambil butir soal `question_type = 'pre_test'` yang ditargetkan untuk modul tersebut (`module_target`).
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "module_id": 1,
      "module_number": 1,
      "module_title": "Anatomi, Fisiologi, & Higienitas Reproduksi",
      "total_questions": 2,
      "questions": [
        {
          "id": 1,
          "module_target": 1,
          "question_text": "Arah yang benar saat membersihkan...",
          "options": [
            { "label": "A", "text": "..." },
            { "label": "B", "text": "..." },
            { "label": "C", "text": "..." },
            { "label": "D", "text": "..." }
          ]
        }
      ]
    }
  }
  ```

### 3.4. Submit Pre-Test Modul (Membuka Akses Materi & Flipbook PDF)
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
      "module_id": 1,
      "module_number": 1,
      "total_questions": 2,
      "correct_answers": 2,
      "score": 100,
      "points_earned": 25
    }
  }
  ```

### 3.5. Baca Submateri & Streaming Berkas PDF Flipbook
- **Method & URL Metadata**: `GET /api/v1/modules/{moduleId}/topic/{topicId}` *(Auth Required)*
- **Method & URL Streaming PDF**: `GET /api/v1/modules/{moduleId}/topic/{topicId}/pdf` *(Auth Required, mengembalikan binary `application/pdf` dengan header CORS untuk pembaca flipbook)*
- **Catatan**: Mengembalikan `403 Forbidden` (`requires_pretest: true`) apabila mahasiswa belum mengerjakan Pre-Test modul terkait. Saat seluruh subbab di semua modul selesai dibaca, `posttest_gate.all_modules_completed_at` otomatis terisi.
- **Response Metadata `200 OK`**:
  ```json
  {
    "success": true,
    "data": {
      "topic": {
        "id": 1,
        "topic_code": "1.1",
        "title": "Pengenalan Anatomi Organ Reproduksi Pria & Wanita",
        "format": "pdf_flipbook",
        "has_pdf": true,
        "pdf_url": "http://localhost:8000/api/v1/modules/1/topic/1/pdf",
        "pdf_original_name": "subbab-1-1.pdf",
        "pdf_size": 245760,
        "youtube_video_id": "v3F4QW_h32M",
        "youtube_embed_url": "https://www.youtube.com/embed/v3F4QW_h32M",
        "content_html": null
      },
      "points_earned": 10,
      "all_topics_completed": false,
      "ready_for_posttest": false,
      "posttest_gate": { ... }
    }
  }
  ```

### 3.6. Cek Status & Hitung Mundur 3 Hari Post-Test Terpadu
- **Method & URL**: `GET /api/v1/posttest/status` *(Auth Required)*
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Status kelayakan Post-Test berhasil dimuat.",
    "data": {
      "pretest_average_score": 75.0,
      "posttest_gate": {
        "state": "waiting_delay",
        "total_modules": 4,
        "completed_modules": 4,
        "total_topics": 16,
        "completed_topics": 16,
        "all_modules_completed": true,
        "all_modules_completed_at": "2026-11-25T10:00:00+07:00",
        "posttest_available_at": "2026-11-28T10:00:00+07:00",
        "is_posttest_unlocked": false,
        "remaining_seconds": 259200,
        "delay_days": 3,
        "has_completed_posttest": false,
        "latest_posttest": null
      }
    }
  }
  ```

### 3.7. Ambil Soal Post-Test Terpadu
- **Method & URL**: `GET /api/v1/posttest` *(Auth Required)*
- **Response `403 Forbidden`** (jika modul belum selesai atau masih dalam masa jeda 3 hari):
  - `reason`: `"modules_incomplete"` atau `"waiting_period"`, disertai `data.posttest_gate`.
- **Response `200 OK`** (setelah melewati jeda 3 hari):
  ```json
  {
    "success": true,
    "message": "Soal Post-Test berhasil dimuat.",
    "data": {
      "pretest_average_score": 75.0,
      "total_questions": 6,
      "questions": [
        {
          "id": 9,
          "question_text": "Seorang mahasiswi mengalami siklus haid...",
          "options": [
            { "label": "A", "text": "..." },
            { "label": "B", "text": "..." },
            { "label": "C", "text": "..." },
            { "label": "D", "text": "..." }
          ]
        }
      ],
      "posttest_gate": { ... }
    }
  }
  ```

### 3.8. Submit Post-Test Terpadu (Hitung N-Gain Total, Tetap Login)
- **Method & URL**: `POST /api/v1/posttest` *(Auth Required)*
- **Request Body**:
  ```json
  {
    "answers": {
      "9": "B",
      "10": "C"
    }
  }
  ```
- **Response `200 OK`**:
  ```json
  {
    "success": true,
    "message": "Post-Test selesai dikerjakan. Skor akhir dan N-Gain telah disimpan.",
    "data": {
      "pretest_score": 60.0,
      "posttest_score": 100,
      "n_gain_score": 1.0,
      "effectiveness_category": "Tinggi",
      "correct_answers": 6,
      "total_questions": 6,
      "points_earned": 100
    }
  }
  ```
- *Catatan migrasi*: Endpoint lama `GET/POST /api/v1/modules/{id}/posttest` mengembalikan `410 Gone` (`redirect_endpoint: "/api/v1/posttest"`).

### 3.9. Pengelolaan Bank Soal Evaluasi (Khusus Admin)
Seluruh endpoint berikut memerlukan token pengguna dengan `role = 'admin'`:
- **Kelola Soal Pre-Test (Berdasarkan Modul)**:
  - `GET /api/v1/admin/pretest-questions?module_number=1` -> Daftar soal `pre_test` (opsional difilter per nomor modul).
  - `POST /api/v1/admin/pretest-questions` -> Tambah soal Pre-Test (`module_target` wajib, `question_text`, `options` atau `option_a..option_d`, `correct_answer`, `explanation`).
  - `PUT /api/v1/admin/pretest-questions/{id}` -> Perbarui soal Pre-Test.
  - `DELETE /api/v1/admin/pretest-questions/{id}` -> Hapus soal Pre-Test.
- **Kelola Soal Post-Test (Terpisah, Umum Tanpa Label Modul)**:
  - `GET /api/v1/admin/posttest-questions` -> Daftar seluruh soal `post_test` (`module_target = null`).
  - `POST /api/v1/admin/posttest-questions` -> Tambah soal Post-Test (`question_text`, `options` atau `option_a..option_d`, `correct_answer`, `explanation`).
  - `PUT /api/v1/admin/posttest-questions/{id}` -> Perbarui soal Post-Test.
  - `DELETE /api/v1/admin/posttest-questions/{id}` -> Hapus soal Post-Test.

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

- `GET /api/v1/gamification/diary` &rarr; Daftar riwayat catatan mood.
- `POST /api/v1/gamification/diary` &rarr; Simpan catatan mood (`mood`, `encrypted_note`, `entry_date`).
