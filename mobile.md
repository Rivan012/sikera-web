# Panduan Membuat Aplikasi Mobile SIKERA

Panduan ini menjelaskan cara membangun aplikasi SIKERA dengan Flutter dan menghubungkannya ke REST API Laravel di repo ini. Aplikasi mobile ditujukan untuk alur mahasiswa. Rute untuk pengelolaan modul dan impor data bukan bagian dari antarmuka mahasiswa.

Gunakan [API_DOCUMENTATION.md](API_DOCUMENTATION.md) sebagai rujukan format request dan response. Panduan ini mencatat rute yang tersedia sekarang, bukan seluruh fitur yang direncanakan dalam PRD.

## 1. Siapkan backend untuk perangkat uji

Jalankan Laravel dari root repo:

```sh
php artisan serve --host=0.0.0.0 --port=8000
```

Pastikan API merespons dengan membuka `GET /api/v1/health`.

| Perangkat | Base URL pengembangan |
| --- | --- |
| Android Emulator | `https://sikera.my.id/api/v1` |
| iOS Simulator | `http://127.0.0.1:8000/api/v1` |
| Ponsel fisik di jaringan yang sama | `http://<IP-LAN-komputer>:8000/api/v1` |

Ponsel fisik harus dapat menjangkau komputer yang menjalankan Laravel. Periksa firewall jika koneksi gagal. Gunakan HTTPS untuk server produksi. Jangan mengaktifkan HTTP tanpa enkripsi pada build rilis.

## 2. Buat proyek Flutter

Dari root repo, buat aplikasi di folder `mobile`:

```sh
flutter create mobile
cd mobile
flutter pub add http flutter_secure_storage
```

Gunakan `http` untuk request REST dan `flutter_secure_storage` untuk token. Simpan nilai API base URL di konfigurasi build, bukan di banyak file atau dalam kode yang dikirim ke produksi.

Contoh menjalankan build pengembangan di Android Emulator:

```sh
flutter run --dart-define=API_BASE_URL=https://sikera.my.id/api/v1
```

Contoh deklarasi konfigurasi:

```dart
const apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'https://sikera.my.id/api/v1',
);
```

Untuk produksi, berikan base URL HTTPS melalui konfigurasi build yang sesuai.

## 3. Susun kode per fitur

Pisahkan komunikasi jaringan dan penyimpanan token dari widget layar. Struktur awal yang bisa digunakan:

```text
lib/
  app.dart
  main.dart
  core/
    config/
    network/
    storage/
  features/
    auth/
    dashboard/
    modules/
    selfcare/
    posters/
    community/
    diary/
```

Buat model berdasarkan response API yang benar-benar dipakai. Hindari menyalin seluruh response ke satu model besar, karena tiap layar hanya perlu sebagian data.

## 4. Hubungkan aplikasi ke API

Kirim header berikut untuk request JSON:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <TOKEN>
```

Header `Authorization` hanya diperlukan pada rute yang dilindungi Sanctum. Jangan kirim `Content-Type: application/json` untuk request yang mengunggah berkas dalam format multipart.

Contoh pemeriksaan koneksi menggunakan paket `http`:

```dart
import 'dart:convert';

import 'package:http/http.dart' as http;

Future<void> checkApi() async {
  final response = await http.get(
    Uri.parse('$apiBaseUrl/health'),
    headers: const {'Accept': 'application/json'},
  );

  if (response.statusCode != 200) {
    throw StateError('API health check returned ${response.statusCode}.');
  }

  final body = jsonDecode(response.body) as Map<String, dynamic>;
  if (body['success'] != true) {
    throw StateError('API health check failed.');
  }
}
```

Buat satu lapisan `ApiClient` untuk menambahkan base URL dan header, mengubah response JSON menjadi data bertipe, serta meneruskan error ke fitur pemanggil. Jangan mengubah error menjadi data kosong, karena layar perlu membedakan hasil kosong dari request yang gagal.

Tangani status HTTP sesuai konteks:

| Status | Penanganan di aplikasi |
| --- | --- |
| `401` | Tampilkan pesan autentikasi. Jika token tidak valid, hapus token lokal dan minta pengguna masuk kembali. |
| `403` | Jelaskan bahwa aksi belum dapat dilakukan, misalnya materi masih menunggu pre-test modul. |
| `404` | Tampilkan bahwa data atau rute yang diminta tidak ditemukan. |
| `422` | Baca objek `errors` dan tampilkan validasi pada field terkait. |
| `5xx` atau gagal koneksi | Tampilkan kegagalan request dan sediakan aksi untuk mencoba kembali. |

Setiap layar yang memuat data perlu menangani status memuat, data kosong, dan gagal memuat.

## 5. Bangun alur akun

1. Tampilkan form masuk dengan email dan kata sandi. Kirim ke `POST /auth/login`.
2. Registrasi mahasiswa memakai `POST /auth/register`. Field wajib saat ini: `name`, `usia` (15 sampai 40), `gender` (`L` atau `P`), `agama`, `prodi`, `pendidikan_terakhir`, `email`, `password`, dan `password_confirmation`. `initials`, `fakultas`, dan `nim` boleh dikirim sebagai field tambahan.
3. Simpan `data.token` ke penyimpanan aman perangkat, bukan `SharedPreferences` atau file biasa.
4. Saat aplikasi dibuka kembali, panggil `GET /auth/me` dengan Bearer token sebelum menampilkan layar terproteksi.
5. Untuk memperbarui biodata, kirim `PUT /auth/biodata`. Backend meminta `usia`, `gender`, `agama`, `prodi`, dan `pendidikan_terakhir`; `initials` dan `fakultas` bersifat opsional.
6. Saat pengguna keluar, panggil `POST /auth/logout`, lalu hapus token lokal. Jangan mencatat token atau kata sandi ke log.

API menggunakan login token Sanctum. Jangan menyimpan kata sandi setelah proses masuk.

## 6. Implementasikan fitur sesuai alur API

Semua path pada tabel berikut relatif terhadap base URL API, misalnya `/api/v1`.

| Fitur | Rute utama | Catatan implementasi |
| --- | --- | --- |
| Beranda | `GET /dashboard` | Gunakan ringkasan akun, modul, poster, dan self-care dari satu response. |
| Daftar modul | `GET /modules` | Tampilkan progres dan status pre-test/post-test yang dikembalikan server. |
| Detail modul | `GET /modules/{id}` | Tampilkan submateri dan status akses. |
| Pre-test modul | `GET` dan `POST /modules/{id}/pretest` | Kirim `answers` sebagai objek dengan ID soal sebagai key, misalnya `"12": "B"`. Tandai selesai setelah server mengonfirmasi response berhasil. |
| Baca submateri | `GET /modules/{id}/topic/{topicId}` | Render `content_html` dengan renderer HTML yang membatasi konten aktif. Gunakan ID video YouTube dari response jika tersedia. |
| Post-test modul | `GET` dan `POST /modules/{id}/posttest` | Kirim jawaban dengan bentuk `answers` yang sama. Tampilkan hasil dan N-Gain dari response server. |
| Banner modul | `GET /modules/{id}/banner` | Rute publik mengirim PNG, JPG/JPEG, atau SVG sesuai berkas banner. Gunakan URL `banner_image` dari response dan jangan menambahkan ekstensi sendiri. |
| Self-care | `GET /selfcare/summary`, `POST /selfcare/period`, `GET /selfcare/period/history`, `GET /selfcare/period/prediction`, `POST /selfcare/bmi`, `GET /selfcare/bmi/history` | Rute ini memerlukan token. |
| Kalkulator & panduan | `POST /selfcare/period/calculate`, `POST /selfcare/period/walidd/calculate`, `POST /selfcare/period/pbac/calculate`, `GET /selfcare/guides/blood`, `GET /selfcare/guides/hygiene` | Rute publik. |
| Poster | `GET /posters`, `POST /posters/{id}/download`, `POST /posters/{id}/share` | Request download/share mencatat aksi. Ambil konten gambar dari data poster, bukan dari response pencatatan aksi. |
| Studi kasus | `GET /cases` dan `GET /cases/{id}` | Memerlukan token. |
| Forum | `GET` dan `POST /forum`, `GET /forum/{id}`, `POST /forum/{id}/reply` | Memerlukan token. Ikuti nama field request pada dokumentasi API. |
| Diary | `GET` dan `POST /gamification/diary` | Memerlukan token. Field simpan mencakup `mood`, `encrypted_note`, dan `entry_date`. |

API saat ini menyediakan pre-test dan post-test per modul. Tidak ada rute global `/pretest` atau `/posttest` untuk aplikasi mobile. Rute trivia dan mitos-fakta juga tidak tersedia di API mobile saat ini, jadi jangan tampilkan aksi tersebut sebagai fitur aktif.

Untuk diary, sepakati format dan pengelolaan kunci enkripsi dengan backend sebelum mengirim catatan. Nama field `encrypted_note` saja tidak membuktikan bahwa aplikasi sudah mengenkripsi isi catatan. Jangan kirim catatan sensitif dalam bentuk teks biasa.

## 7. Terapkan arah desain SIKERA

Ikuti [DESIGN.md](DESIGN.md) saat merancang layar. Arahnya adalah publikasi edukasi kesehatan kampus yang hangat, empatik, ilmiah, dan tidak vulgar, dengan dial `ENERGY 2 / RHYTHM 3 / MOTION 1`.

- Gunakan warm paper sebagai latar, botanical pine untuk identitas utama, terracotta untuk penanda klinis, dan ochre sebagai aksen terbatas.
- Gunakan Newsreader untuk judul editorial dan angka indeks jika font tersedia untuk aplikasi, serta Plus Jakarta Sans untuk isi dan kontrol.
- Variasikan komposisi mengikuti fungsi layar. Daftar materi, formulir, kalender, dan konten klinis tidak harus memakai satu pola kartu.
- Jaga target sentuh minimal 44 x 44 px, fokus terlihat, teks tidak terpotong, dan dukung ukuran layar ponsel maupun tablet.
- Sediakan tema terang dan gelap sesuai token di `DESIGN.md`. Uji kontras serta setiap status layar pada keduanya.
- Batasi animasi pada perubahan status yang membantu pengguna, dan hormati pengaturan reduced motion.

Rencanakan navigasi dari layar yang benar-benar dibuat. Jangan menambahkan menu untuk fitur yang belum punya alur mobile atau endpoint.

## 8. Uji sebelum rilis

Jalankan pemeriksaan Flutter dari folder `mobile`:

```sh
flutter analyze
flutter test
```

Uji alur berikut pada emulator dan perangkat fisik:

- Masuk, registrasi, pemulihan sesi, biodata, dan keluar.
- Penolakan token tidak valid serta response validasi `422`.
- Pre-test, akses topik, progres materi, dan post-test pada satu modul.
- Banner SVG serta banner PNG/JPG dengan URL yang diberikan API.
- Self-care, riwayat, poster, studi kasus, forum, dan diary.
- Kondisi jaringan lambat, putus, data kosong, dan server error.

Jangan tandai fitur selesai hanya karena layar berhasil dibuka. Pastikan request berhasil, error ditampilkan, dan perubahan data sesuai response backend.

## Referensi repo

- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) untuk payload dan contoh response.
- [DESIGN.md](DESIGN.md) untuk warna, tipografi, dan arah antarmuka.
- [PRD.md](PRD.md) untuk sasaran produk dan fitur yang direncanakan.
- [alur_sistem_aplikasi.md](alur_sistem_aplikasi.md) untuk alur pengguna.
