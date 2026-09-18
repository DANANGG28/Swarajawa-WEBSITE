# Panduan Eksekusi: Korpus RAG Sinau (Berbasis File)

Dokumen ini adalah instruksi kerja untuk AI/developer yang akan mengeksekusi pembangunan korpus RAG untuk chatbot Sinau (aplikasi belajar bahasa & budaya Jawa). Baca seluruh dokumen sebelum mulai eksekusi.

## Konteks Proyek

Sinau adalah aplikasi mobile + website (Laravel + PostgreSQL untuk backend, Flutter untuk mobile) untuk siswa SMP/SMA belajar bahasa dan budaya Jawa. Chatbot RAG yang dibangun harus **guardrailed** — hanya menjawab dalam lingkup bahasa/budaya Jawa, menolak dengan sopan pertanyaan di luar topik.

Codebase RAG sudah ada dan JANGAN dibuat ulang dari nol:
- `app/Models/Korpus.php`
- `database/migrations/2026_01_01_001200_create_korpus_table.php`
- `app/Services/Ai/RagService.php` (sudah ada guardrail keyword similarity & refusal)
- `database/seeders/KorpusSeeder.php`
- Endpoint `POST /api/chat`

## Keputusan Arsitektur (SUDAH FINAL — jangan diubah tanpa konfirmasi user)

**Korpus disimpan sebagai file, BUKAN di-embed ke vector database.** Alasan: skala data kecil (ribuan baris teks pendek), proyek tugas kuliah, dan retrieval berbasis keyword similarity (bukan semantic/vector search) sudah cukup untuk kebutuhan guardrail chatbot ini. Ini membuat sistem ringan — tidak perlu pgvector, tidak perlu API embedding berbayar, tidak perlu infra tambahan.

**Wikipedia Basa Jawa (materi budaya) TIDAK dikerjakan di tahap ini.** Fokus dulu ke 2 sumber dataset di bawah. Materi budaya dari Wikipedia menyusul di iterasi berikutnya (butuh proses kurasi judul artikel secara manual dulu — belum siap).

## Sumber Dataset yang Dikerjakan Sekarang

### 1. JavaneseHonorifics/Unggah-Ungguh (Utama)
- Link: https://huggingface.co/datasets/JavaneseHonorifics/Unggah-Ungguh
- Lisensi: **CC-BY-NC 4.0** (non-komersial — wajib dicantumkan sebagai atribusi sumber, jangan dihapus)
- Dua subset yang harus diambil:
  - `translation` (~4.024 baris): kolom `index`, `label` (0=Ngoko, 1=Ngoko Alus, 2=Krama, 3=Krama Alus), `javanese_sentence`, `group`, `indonesian_sentence`, `english_sentence`
  - `conversation` (80 baris): kolom `index`, `role_a`, `role_b`, `context`, `a_utterance`, `a_utterance_category`, `b_utterance`, `b_utterance_category`
- File tersedia di tab "Files and versions" dataset tsb dalam format CSV/Parquet.

### 2. NgokoKrama (Pelengkap)
- 1.000 pasang kalimat Indonesia ↔ Jawa Krama.
- **PENTING**: link dataset ini belum dikonfirmasi persis (user hanya menyebut nama dataset dan domain huggingface.co/datasets tanpa path lengkap). Sebelum eksekusi bagian ini, cari dataset ini di Hugging Face dengan kata kunci "NgokoKrama" atau "Ngoko Krama Indonesian Javanese", verifikasi nama repo persis, lisensinya, dan struktur kolomnya. Jangan menebak nama file/kolom.

### 3. afrizalha/Gatra-1-Javanese (Opsional — kerjakan setelah #1 dan #2 selesai)
- Link: https://huggingface.co/datasets/afrizalha/Gatra-1-Javanese
- QA Krama sintetis terkurasi. Cek struktur kolom aktual di halaman dataset sebelum mapping ke skema korpus (kemungkinan besar formatnya question-answer, beda struktur dari dua dataset di atas — jangan asumsikan sama).

### Dataset yang DILARANG dipakai
- **Baoesastra Djawa** — dilarang karena hak cipta masih berlaku (70 tahun pasca wafat penulis). Jangan gunakan dalam bentuk apa pun, termasuk sebagai referensi tidak langsung.

## Format File Korpus (Target Output)

Simpan hasil olahan sebagai file JSON per kategori di `storage/app/corpus/`, bukan sebagai satu file raksasa:

```
storage/app/corpus/
├── unggah-ungguh-translation.json
├── unggah-ungguh-conversation.json
└── ngoko-krama.json
```

Setiap file berisi array of object dengan skema yang **harus mengikuti kolom `$fillable` di `app/Models/Korpus.php` yang sudah ada** — jangan membuat skema baru. Buka file model tersebut terlebih dahulu untuk memastikan nama kolom persis (kemungkinan `judul`, `kategori`, `konten`, `sumber` berdasarkan konteks PRD, tapi WAJIB diverifikasi ke file asli sebelum mapping, karena nama kolom yang salah akan membuat seeder gagal insert).

Contoh struktur (sesuaikan nama key persis dengan `$fillable`):

```json
[
  {
    "judul": "Ngoko - Kowe mangan apa dina iki?",
    "kategori": "unggah-ungguh-translation",
    "konten": "Kalimat Jawa (Ngoko): \"Kowe mangan apa dina iki?\" | Bahasa Indonesia: \"Kamu makan apa hari ini?\" | English: \"What did you eat today?\"",
    "sumber": "JavaneseHonorifics/Unggah-Ungguh (CC-BY-NC 4.0) — https://huggingface.co/datasets/JavaneseHonorifics/Unggah-Ungguh"
  }
]
```

Untuk subset `conversation`, gabungkan context + kedua utterance jadi satu `konten` per baris (bukan dipecah jadi 2 entry terpisah), supaya konteks percakapan tidak hilang saat di-retrieve:

```json
{
  "judul": "Percakapan Guru-Murid: menanyakan makanan",
  "kategori": "unggah-ungguh-conversation",
  "konten": "Konteks: Guru bertanya ke murid soal makanan hari ini (guru berstatus lebih tinggi). Guru: \"Kowe mangan apa dina iki?\" (Ngoko). Murid: \"Kula nedha pecel dinten puniki.\" (Krama).",
  "sumber": "JavaneseHonorifics/Unggah-Ungguh (CC-BY-NC 4.0)"
}
```

## Langkah Eksekusi

1. **Verifikasi skema `Korpus` model dan migration terlebih dahulu.** Buka `app/Models/Korpus.php` dan migration-nya, catat nama kolom persis dan tipe datanya (string/text, ada limit panjang atau tidak).

2. **Download dataset.** Ambil file CSV/Parquet dari halaman Hugging Face masing-masing dataset (lihat link di atas). Untuk NgokoKrama, cari dulu repo yang benar sebelum download.

3. **Tulis script konversi** (boleh PHP artisan command sekali pakai, atau Python, sesuaikan mana yang lebih cepat dikerjakan) yang membaca CSV mentah dan mengubahnya ke format JSON sesuai skema di atas. Simpan hasilnya ke `storage/app/corpus/`.

4. **Update `KorpusSeeder.php`** supaya membaca file-file JSON tersebut dan insert ke tabel, alih-alih hardcode array data langsung di file seeder. Pola:
   ```php
   public function run(): void
   {
       $files = [
           'unggah-ungguh-translation.json',
           'unggah-ungguh-conversation.json',
           'ngoko-krama.json',
       ];

       foreach ($files as $file) {
           $path = storage_path("app/corpus/{$file}");
           if (!file_exists($path)) {
               continue; // lewati kalau file belum ada, jangan error
           }
           $rows = json_decode(file_get_contents($path), true);
           foreach ($rows as $row) {
               Korpus::create($row);
           }
       }
   }
   ```

5. **Jalankan seeder**: `php artisan db:seed --class=KorpusSeeder`

6. **Verifikasi `RagService.php` tidak perlu diubah** kalau guardrail keyword similarity-nya sudah generic (bekerja di atas kolom `konten` apa pun isinya). Kalau ternyata `RagService` punya asumsi kaku soal format data lama, sesuaikan seperlunya — tapi jangan ubah logic guardrail/refusal yang sudah ada tanpa alasan kuat.

7. **Jalankan test**: `DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=RagServiceTest`

8. **Cek manual via endpoint** `POST /api/chat` dengan beberapa pertanyaan uji:
   - Pertanyaan dalam lingkup (contoh: "Bedanya Ngoko sama Krama apa?", "Kalau ke guru pakai bahasa apa?") — harus dapat jawaban relevan dari korpus.
   - Pertanyaan di luar lingkup (contoh: "Siapa presiden Indonesia?") — harus ditolak sopan oleh guardrail.

## Konfigurasi AI Provider & Keamanan API Key

Model yang dipakai: **Gemini 3.8 Flash**, diakses lewat **custom provider/proxy** di endpoint `http://localhost:20128/v1` (kemungkinan proxy lokal/gateway yang expose format OpenAI-compatible — verifikasi dulu apakah proxy ini butuh API key sendiri atau meneruskan ke API key Gemini asli di belakangnya).

### Penyimpanan key

1. **Jangan pernah hardcode API key di kode** (termasuk di `RagService.php`, config file yang di-commit, atau di `KorpusSeeder.php`). Semua key wajib lewat `.env`.

2. Tambahkan ke `.env` (jangan commit file ini — pastikan `.env` ada di `.gitignore`):
   ```
   AI_PROVIDER_BASE_URL=http://localhost:20128/v1
   AI_PROVIDER_API_KEY=isi-key-di-sini
   AI_PROVIDER_MODEL=gemini-3.8-flash
   ```

3. Buat file config terpisah `config/ai.php` (jangan taruh langsung `env()` di service class — best practice Laravel supaya bisa di-cache dengan `config:cache`):
   ```php
   return [
       'base_url' => env('AI_PROVIDER_BASE_URL'),
       'api_key' => env('AI_PROVIDER_API_KEY'),
       'model' => env('AI_PROVIDER_MODEL', 'gemini-3.8-flash'),
   ];
   ```

4. Di `RagService.php`, ambil dari config, bukan `env()` langsung:
   ```php
   $response = Http::withHeaders([
       'Authorization' => 'Bearer ' . config('ai.api_key'),
   ])->timeout(30)->post(config('ai.base_url') . '/chat/completions', [
       'model' => config('ai.model'),
       'messages' => $messages,
   ]);
   ```

5. Sediakan `.env.example` dengan key **kosong** (`AI_PROVIDER_API_KEY=`) supaya anggota tim tahu variabel apa yang perlu diisi, tanpa expose key asli di repo.

### Karena endpoint-nya `localhost`

Karena base URL mengarah ke `localhost:20128`, ini kemungkinan proxy yang jalan di mesin/server yang sama dengan aplikasi Laravel (misal LiteLLM proxy, atau custom gateway buatan tim). Catatan penting:

- **Ini hanya akan bekerja di environment tempat proxy tersebut berjalan.** Kalau nanti deploy Laravel ke server terpisah dari proxy-nya (misal Laravel di satu container Dokploy, proxy di container lain), `localhost` tidak akan bisa diakses — harus diganti ke hostname/service-name container yang benar (misal `http://ai-proxy:20128/v1` kalau pakai Docker network internal). Pastikan `AI_PROVIDER_BASE_URL` gampang diganti per environment lewat `.env`, jangan di-hardcode sebagai `localhost` di config default.
- Jangan expose port proxy ini ke publik internet tanpa autentikasi tambahan — kalau proxy tidak minta API key sendiri dan cuma meneruskan ke key Gemini asli, siapa pun yang bisa akses port itu bisa memakai kuota Gemini kamu secara bebas.
- Log request ke proxy ini sebaiknya tidak mencatat isi header `Authorization` (banyak HTTP client Laravel/Guzzle logging middleware secara default mencatat header — cek dan redact kalau ada logging aktif).

### Guardrail tambahan terkait biaya

Karena ini chatbot untuk siswa SMP/SMA yang berpotensi dipakai banyak orang saat demo, tambahkan rate limiting sederhana di endpoint `POST /api/chat` (misal pakai `throttle` middleware bawaan Laravel) supaya tidak ada penyalahgunaan yang menghabiskan kuota API key secara tidak sengaja.

## Yang TIDAK boleh dilakukan tanpa konfirmasi user

- Jangan pindah arsitektur ke pgvector/vector DB — keputusan file-based sudah final untuk tahap ini.
- Jangan menambahkan Wikipedia Basa Jawa — di luar scope tahap ini.
- Jangan mengubah skema kolom `Korpus` model/migration yang sudah ada.
- Jangan menebak nama repo/kolom dataset NgokoKrama — cari dan verifikasi dulu.
- Jangan gunakan Baoesastra Djawa dalam bentuk apa pun.
- Jangan hardcode API key AI provider di mana pun di kode atau config yang ter-commit ke git.
