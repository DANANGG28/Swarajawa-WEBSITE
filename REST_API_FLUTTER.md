# Dokumentasi Integrasi REST API Sinau Jowo (Swarajawa) ke Flutter

Dokumen ini berisi spesifikasi lengkap REST API backend Laravel Sinau Jowo untuk keperluan integrasi dengan aplikasi Flutter.

---

## 1. Konfigurasi Umumm & Header Request

### Base URL
```text
http://<SERVER_IP_ATAU_DOMAIN>:8000/api
```

### Headers Default
| Header | Value | Keterangan |
|---|---|---|
| `Accept` | `application/json` | Wajib untuk format respon JSON |
| `Content-Type` | `application/json` | Wajib untuk request body JSON |
| `Authorization` | `Bearer <TOKEN_SANCTUM>` | Wajib untuk endpoint yang butuh autentikasi |

---

## 2. Health Check System

### `GET /health`
Mengecek ketersediaan server API.

* **Auth Needed:** No
* **Response `200 OK`:**
```json
{
  "app": "Sinau Jowo API",
  "status": "ok",
  "time": "2026-09-16T10:00:00.000000Z"
}
```

---

## 3. Autentikasi & Akun (`/auth` & `/me`)

### A. Registrasi Mandiri Siswa
`POST /auth/siswa/register`

* **Auth Needed:** No
* **Request Body:**
```json
{
  "nis": "123456789",
  "nama_lengkap": "Budi Santoso",
  "jenis_kelamin": "L",
  "kelas": "7A",
  "no_telpon": "081234567890",
  "email": "budi@gmail.com",
  "password": "password123"
}
```
* **Response `201 Created`:**
```json
{
  "message": "Registrasi siswa berhasil.",
  "role": "siswa",
  "user": {
    "id": 1,
    "nis": "123456789",
    "nama_lengkap": "Budi Santoso",
    "email": "budi@gmail.com"
  },
  "token": "1|abcdef123456..."
}
```

---

### B. Login Siswa
`POST /auth/siswa/login` *(atau Login Terpadu: `POST /auth/login`)*

* **Auth Needed:** No
* **Request Body:**
```json
{
  "email": "budi@gmail.com",
  "password": "password123"
}
```
* **Response `200 OK`:**
```json
{
  "message": "Login berhasil.",
  "role": "siswa",
  "user": {
    "id": 1,
    "nama_lengkap": "Budi Santoso",
    "email": "budi@gmail.com"
  },
  "token": "1|abcdef123456..."
}
```

---

### C. Get Current User Info
`GET /me`

* **Auth Needed:** Yes (`Bearer <token>`)
* **Response `200 OK`:**
```json
{
  "role": "siswa",
  "user": {
    "id": 1,
    "nama_lengkap": "Budi Santoso",
    "email": "budi@gmail.com"
  },
  "exp": {
    "id": 1,
    "total_exp": 150
  },
  "strek": {
    "id": 1,
    "current_streak": 3,
    "highest_streak": 5
  }
}
```

---

### D. Logout
`POST /auth/logout`

* **Auth Needed:** Yes (`Bearer <token>`)
* **Response `200 OK`:**
```json
{
  "message": "Berhasil keluar."
}
```

---

## 4. Pembelajaran & Kuis (`/materi` & `/kuis`)

### A. Daftar Level Materi & Status Progres
`GET /materi`

* **Auth Needed:** Yes (Siswa)
* **Response `200 OK`:**
```json
{
  "data": [
    {
      "id": 1,
      "nama_materi": "Aksara Jawa Dasar",
      "deskripsi": "Belajar aksara carakan 20 karakter",
      "reward_exp": 50,
      "urutan": 1,
      "jumlah_soal": 5,
      "status": "terbuka",
      "tanggal_selesai": null
    },
    {
      "id": 2,
      "nama_materi": "Sandhangan Swara",
      "deskripsi": "Belajar wulu, pepet, suku, taling, taling tarung",
      "reward_exp": 100,
      "urutan": 2,
      "jumlah_soal": 5,
      "status": "terkunci",
      "tanggal_selesai": null
    }
  ]
}
```

---

### B. Detail Level & Daftar Soal
`GET /materi/{levelMateriId}`

* **Auth Needed:** Yes (Siswa)
* **Response `200 OK`:**
```json
{
  "data": {
    "level_materi": {
      "id": 1,
      "nama_materi": "Aksara Jawa Dasar",
      "deskripsi": "Belajar aksara carakan 20 karakter",
      "reward_exp": 50,
      "urutan": 1
    },
    "status": "terbuka",
    "soal": [
      {
        "id": 10,
        "jenis_soal": "pilihan_ganda",
        "pertanyaan": "Aksara 'Ha' dalam aksara Jawa yaiku...",
        "opsi_jawaban": ["ꦲ", "ꦤ", "ꦯ", "ꦫ"],
        "bobot_exp": 10
      }
    ]
  }
}
```

---

### C. Mulai Sesi Kuis Level
`POST /materi/{levelMateriId}/mulai`

* **Auth Needed:** Yes (Siswa)
* **Response `200 OK`:**
```json
{
  "message": "Sesi quiz siap dimulai.",
  "level_materi": {
    "id": 1,
    "nama_materi": "Aksara Jawa Dasar",
    "reward_exp": 50
  },
  "soal": [...]
}
```
* **Error Response `403 Forbidden`** (Jika level masih terkunci):
```json
{
  "message": "Materi belum tercapai. Selesaikan materi prasyarat terlebih dahulu."
}
```

---

### D. Jawab Butir Soal Kuis
`POST /kuis/jawab`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "soal_id": 10,
  "jawaban": "ꦲ"
}
```
* **Response `200 OK`:**
```json
{
  "message": "Jawaban benar!",
  "benar": true,
  "skor": 100,
  "detail": "Kriteria penilaian terpenuhi",
  "kunci_jawaban": "ꦲ",
  "exp_didapat": 10,
  "total_exp": 160,
  "current_streak": 4,
  "highest_streak": 5
}
```

---

### E. Selesaikan Level Kuis
`POST /kuis/selesai`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "level_materi_id": 1
}
```
* **Response `200 OK`:**
```json
{
  "message": "Level Aksara Jawa Dasar selesai.",
  "reward_exp": 50,
  "total_exp": 210,
  "current_streak": 4
}
```

---

## 5. Gamifikasi & Progres (`/progres` & `/leaderboard`)

### A. Ringkasan & Detail Progres Siswa
`GET /progres`

* **Auth Needed:** Yes (Siswa)
* **Response `200 OK`:**
```json
{
  "ringkasan": {
    "total_exp": 210,
    "current_streak": 4,
    "highest_streak": 5,
    "level_selesai": 1,
    "total_level": 5,
    "persentase": 20
  },
  "progres": [
    {
      "level_materi_id": 1,
      "nama_materi": "Aksara Jawa Dasar",
      "urutan": 1,
      "status": "selesai",
      "tanggal_selesai": "2026-09-16 10:30:00"
    },
    {
      "level_materi_id": 2,
      "nama_materi": "Sandhangan Swara",
      "urutan": 2,
      "status": "terbuka",
      "tanggal_selesai": null
    }
  ]
}
```

---

### B. Leaderboard (Papan Peringkat)
`GET /leaderboard?kelas=7A&limit=20`

* **Auth Needed:** Yes (Siswa)
* **Query Params (Opsional):**
  * `kelas` (string): Filter berdasarkan kelas
  * `limit` (integer): Jumlah data (1-100, default 20)
* **Response `200 OK`:**
```json
{
  "kelas": "7A",
  "data": [
    {
      "peringkat": 1,
      "siswa_id": 5,
      "nama_lengkap": "Siti Aminah",
      "kelas": "7A",
      "total_exp": 500,
      "current_streak": 10
    },
    {
      "peringkat": 2,
      "siswa_id": 1,
      "nama_lengkap": "Budi Santoso",
      "kelas": "7A",
      "total_exp": 210,
      "current_streak": 4
    }
  ]
}
```

---

## 6. Chatbot AI & Speech Processing (`/chat` & `/speech`)

### A. Chatbot Tanya Jawab AI (RAG)
`POST /chat`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "pertanyaan": "Apa iku sandhangan wulu?"
}
```
* **Response `200 OK`:**
```json
{
  "jawaban": "Sandhangan wulu ( ꦞ ) iku digunakake kanggo ngowahi swara aksara dadi swara 'i'.",
  "sumber": ["Materi Sandhangan Swara"]
}
```

---

### B. Text to Speech (TTS) Bahasa Jawa
`POST /speech/tts`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "teks": "Sugeng enjang, sugeng rawuh wonten aplikasi Sinau Jowo.",
  "voice": "jv-ID-Wavenet-A"
}
```
* **Response `200 OK`:** JSON berisi URL audio atau base64 audio sintetis.

---

### C. Speech to Text (STT) Bahasa Jawa
`POST /speech/stt`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "audio": "base64_encoded_audio_data...",
  "mock_transcript": "sugeng enjang"
}
```
* **Response `200 OK`:**
```json
{
  "transkrip": "sugeng enjang",
  "kepercayaan": 0.95
}
```

---

### D. Speech to Speech (STS) Bahasa Jawa
`POST /speech/sts`

* **Auth Needed:** Yes (Siswa)
* **Request Body:**
```json
{
  "audio": "base64_encoded_audio_data...",
  "teks_referensi": "sugeng enjang"
}
```
* **Response `200 OK`:** Hasil analisis pengucapan & audio balik.

---

## 7. Panduan Integrasi di Flutter

### Contoh Service Http / Dio Client (Flutter)

```dart
import 'package:dio/dio.dart';

class ApiService {
  static final String baseUrl = "http://10.0.2.2:8000/api"; // 10.0.2.2 untuk Android Emulator
  late Dio dio;

  ApiService([String? token]) {
    dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      },
    ));
  }

  // Example Login
  Future<Map<String, dynamic>> loginSiswa(String email, String password) async {
    final response = await dio.post('/auth/siswa/login', data: {
      'email': email,
      'password': password,
    });
    return response.data;
  }

  // Example Get Materi
  Future<List<dynamic>> getMateri() async {
    final response = await dio.get('/materi');
    return response.data['data'];
  }
}
```
