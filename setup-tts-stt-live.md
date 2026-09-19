# Setup TTS & STT Live (Speaking Practice) — Sinau Jowo

Dokumentasi ini mencakup setup untuk **dua fitur speaking practice yang terpisah**, sama-sama pakai kalimat referensi dari `kunci_jawaban` soal, tapi beda total di cara menilai:

| | **Quiz Suara** | **Latihan Ngomong** |
|---|---|---|
| Masuk EXP/skor? | ✅ Ya | ❌ Tidak |
| Cara menilai | Fuzzy string matching (non-LLM) | Gemini 3.8 Flash (generatif, kualitatif) |
| Bentuk hasil | Kategori tetap: Benar/Hampir/Salah | Feedback bebas ("kurang tepat di bagian mana") |

Bukan real-time streaming (bukan kayak telepon) — keduanya pipeline bertahap (turn-based) dengan total latency realistis 2-5 detik per putaran.

## 0. Relasi dengan PRD Sinau Jowo (v1.7)

Dokumen ini **menggantikan keputusan provider** yang tercantum di PRD pada bagian-bagian berikut (PRD sendiri tidak diubah, cukup jadikan dokumen ini acuan implementasi terbaru):

| Bagian PRD | Isi lama di PRD | Diganti jadi (dokumen ini) |
|---|---|---|
| **FR-6** (TTS) | Tidak menyebut provider spesifik | Provider: **edge-tts** dengan voice `jv-ID-DimasNeural`/`jv-ID-SitiNeural` (Bagian 4) |
| **FR-7** (STT) | Tidak menyebut provider spesifik | Provider: **ElevenLabs Scribe** (Bagian 3) |
| **FR-8** & **Bagian 6C — Opsi A** | Pipeline: STT (**Google Cloud**, `jv-ID`) → Modul Pemrosesan Respons → TTS (**Azure AI Speech**) | Dipecah jadi 2 pipeline — lihat Bagian 1A & 1B |
| **Bagian 10.2, poin 2** | Rekomendasi awal: Azure (TTS) + Google Cloud STT | Tidak jadi dipakai — Azure gagal didaftarkan berkali-kali (`"not eligible"` / konflik verifikasi SheerID); STT diganti ElevenLabs Scribe (lebih akurat untuk Jawa, WER ~10-20%, tidak butuh billing GCP) |

**✅ Update dari draf sebelumnya — penyimpangan dari PRD Bagian 6C sudah terselesaikan:**
Draf awal dokumen ini sempat memakai LLM generatif untuk fitur yang masuk skor (bertentangan dengan rekomendasi eksplisit PRD Bagian 6C: *"tidak disarankan memakai LLM generatif bebas untuk merangkai balasan... risiko hasil tidak natural/tidak sesuai unggah-ungguh"*). Ini sudah diperbaiki dengan **memecah fitur jadi dua**:
- **Quiz Suara** (yang masuk EXP, `FR-7`/`FR-8` asli) → tetap murni **fuzzy matching, non-LLM**, sejalan 100% dengan rekomendasi PRD Bagian 6C.
- **Latihan Ngomong** (fitur tambahan baru, di luar cakupan asli FR-7/FR-8) → boleh pakai LLM generatif karena **tidak memengaruhi EXP/skor** — resiko halusinasi Gemini di sini cuma bikin feedback kurang pas, bukan merugikan penilaian siswa.

Kalau tim setuju pembagian ini final, PRD Bagian 6 (tambahkan FR baru untuk Latihan Ngomong) dan Bagian 9 (Risiko) sebaiknya diupdate menyusul — di luar cakupan dokumen ini.

---

## 1A. Arsitektur — Quiz Suara (dinilai, masuk EXP)

```
┌──────────┐  audio.mp3  ┌───────────────────┐  teks siswa  ┌────────────────────┐
│  Flutter │ ───────────►│  ElevenLabs Scribe │─────────────►│  Fuzzy Matching     │
│ (rekam)  │             │   (STT, jav)       │              │  vs kunci_jawaban   │
└──────────┘             └───────────────────┘              │  (non-LLM, PHP)     │
     ▲                                                        └─────────┬──────────┘
     │  audio.mp3 (respons)                                             │ kategori:
     │                                                                  │ benar/hampir/salah
┌──────────────┐   teks respons (sudah disiapkan guru)                  │
│   edge-tts   │ ◄────────────────────────────────────────────────────┘
│ (TTS, Dimas) │
└──────────────┘
```

**Alur step-by-step:**
1. Siswa dapat kalimat referensi (`kunci_jawaban` soal) → rekam suara mencoba mengucapkannya.
2. Laravel kirim audio ke **ElevenLabs Scribe** → dapat teks transkripsi.
3. Teks transkripsi dibandingkan ke `kunci_jawaban` pakai **fuzzy string matching** (Levenshtein) — bukan LLM.
4. Berdasarkan skor kemiripan, sistem pilih salah satu dari **teks respons yang sudah disiapkan guru** per soal (`respons_benar`/`respons_hampir_benar`/`respons_salah`).
5. Teks respons itu dikirim ke **edge-tts** → jadi audio, diputar ke siswa.
6. Skor kemiripan dipakai untuk hitung EXP.

---

## 1B. Arsitektur — Latihan Ngomong (tidak dinilai, tanpa EXP)

```
┌──────────┐  audio.mp3  ┌───────────────────┐  teks siswa  ┌──────────────────┐
│  Flutter │ ───────────►│  ElevenLabs Scribe │─────────────►│   RagService      │
│ (rekam)  │             │   (STT, jav)       │              │ (Gemini 3.8 Flash,│
└──────────┘             └───────────────────┘              │  dikasih kalimat  │
     ▲                                                        │  referensi + hasil│
     │  audio.mp3 (feedback)                                  │  transkripsi)     │
     │                                                        └─────────┬─────────┘
┌──────────────┐   teks feedback kualitatif                             │
│   edge-tts   │ ◄─────────────────────────────────────────────────────┘
│ (TTS, Dimas) │
└──────────────┘
```

**Alur step-by-step:**
1. Siswa dapat kalimat referensi yang sama sumbernya (`kunci_jawaban` soal) → rekam suara.
2. Laravel kirim audio ke **ElevenLabs Scribe** → dapat teks transkripsi.
3. Teks transkripsi **+ kalimat referensi asli** dikirim ke **RagService** (Gemini 3.8 Flash) → diminta kasih feedback kualitatif (bagian mana yang kurang tepat), bukan vonis benar/salah.
4. Teks feedback dikirim ke **edge-tts** → jadi audio, diputar ke siswa.
5. **Tidak ada skor/EXP** dari fitur ini — murni latihan.

**Kenapa aman dipakai generatif:** LLM selalu dikasih kalimat referensi sebagai pembanding (bukan menilai dari kekosongan), jadi feedback tetap terarah. Karena tidak masuk skor, kalaupun sesekali Gemini kasih feedback kurang pas, dampaknya cuma "latihan kurang optimal" — bukan nilai siswa yang salah.

**Kenapa provider ini (bukan Azure langsung), berlaku untuk kedua fitur:**
Sudah dicoba berkali-kali daftar Azure (free trial & pay-as-you-go) dan selalu kena error `"Verification not allowed"` / `"not eligible"` — bug yang cukup umum dilaporkan (konflik antara verifikasi SheerID yang sukses tapi sistem Azure sendiri tetap menolak).

| Kebutuhan | Provider | Kenapa |
|---|---|---|
| TTS | **edge-tts** | Gratis, tanpa akun; numpang endpoint gratis "Read Aloud" Microsoft Edge yang memakai mesin Azure Neural TTS yang sama (`jv-ID-DimasNeural`). SSML penuh didukung karena mesinnya asli Azure. |
| STT | **ElevenLabs Scribe** | Akurasi terbaik untuk Bahasa Jawa (kategori "Good", WER 10-20% di Scribe v2), tidak butuh GPU lokal, free tier ~2.5 jam audio/bulan. |
| Koreksi (khusus Latihan Ngomong) | **RagService (Gemini 3.8 Flash)** | Sudah ada di project, dipakai chatbot RAG lewat proxy `http://localhost:20128/v1`. |

**Catatan penting soal edge-tts:** ini BUKAN API resmi Microsoft — reverse-engineered dari fitur "Read Aloud" di Edge browser. Cocok untuk development/demo/skripsi, tapi bisa berubah/berhenti berfungsi sewaktu-waktu tanpa pemberitahuan. Karena TTS soal juga pakai pola lazy-cache (lihat dokumentasi terpisah), risiko ini hanya berdampak ke audio yang belum pernah digenerate.

---

## 2. Prasyarat

### 2.1 Python + edge-tts (untuk TTS) — manual, dijalankan sendiri
Setup ini **tidak bisa dijalankan otomatis dari sini** (sandbox dokumentasi ini terpisah dari environment project kamu, tanpa akses network ke server/laptop kamu) — ikuti langkah manual, atau pakai script `setup.sh` di Bagian 2.4 supaya tinggal 1x jalan.

```bash
python3 --version   # pastikan Python 3.8+
pip install edge-tts --break-system-packages
```

Tes langsung dari command line:
```bash
edge-tts --voice jv-ID-DimasNeural --text "Sugeng enjing, iki tes swara Dimas" --write-media test.mp3
```
Kalau `test.mp3` terbuat dan bisa diputar, instalasi berhasil.

Cek voice list yang tersedia (opsional, pastikan `jv-ID-DimasNeural` & `jv-ID-SitiNeural` ada):
```bash
edge-tts --list-voices | grep jv-ID
```

### 2.2 Akun ElevenLabs (untuk STT)
1. Daftar di https://elevenlabs.io (cukup email/Google, tanpa kartu kredit untuk free tier).
2. Buka **Profile → API Keys**, generate API key (`xi-api-key`).
3. Simpan key ini, jangan di-commit ke git.

### 2.3 Konfigurasi `.env` Laravel
```env
# ElevenLabs STT
ELEVENLABS_API_KEY=xi-xxxxxxxxxxxxxxxxxxxxx
ELEVENLABS_STT_MODEL=scribe_v2

# edge-tts (dipanggil via exec, tidak butuh API key)
EDGE_TTS_VOICE_DIMAS=jv-ID-DimasNeural
EDGE_TTS_VOICE_SITI=jv-ID-SitiNeural
EDGE_TTS_BINARY=edge-tts   # ganti kalau pakai path python -m edge_tts

# Gemini (sudah ada, dipakai ulang dari RagService — khusus Latihan Ngomong)
GEMINI_PROXY_URL=http://localhost:20128/v1
GEMINI_MODEL=gemini-3.8-flash
```

Tambahkan juga ke `config/services.php`:
```php
'elevenlabs' => [
    'api_key' => env('ELEVENLABS_API_KEY'),
    'stt_model' => env('ELEVENLABS_STT_MODEL', 'scribe_v2'),
],
'edge_tts' => [
    'binary' => env('EDGE_TTS_BINARY', 'edge-tts'),
    'voice_dimas' => env('EDGE_TTS_VOICE_DIMAS', 'jv-ID-DimasNeural'),
    'voice_siti' => env('EDGE_TTS_VOICE_SITI', 'jv-ID-SitiNeural'),
],
```

### 2.4 Script otomatis (opsional, biar setup 1x jalan)
Simpan sebagai `setup-edge-tts.sh` di root project, lalu jalankan `bash setup-edge-tts.sh`:

```bash
#!/bin/bash
set -e

echo "== Cek Python =="
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 belum terinstall. Install dulu (mis. 'sudo apt install python3 python3-pip') lalu jalankan ulang script ini."
    exit 1
fi
python3 --version

echo "== Install edge-tts =="
pip install edge-tts --break-system-packages

echo "== Cek voice Jawa tersedia =="
if edge-tts --list-voices | grep -q "jv-ID-DimasNeural"; then
    echo "✅ jv-ID-DimasNeural ditemukan"
else
    echo "⚠️  jv-ID-DimasNeural tidak ditemukan di voice list — cek koneksi internet atau versi edge-tts"
fi

echo "== Tes generate audio =="
edge-tts --voice jv-ID-DimasNeural --text "Sugeng enjing, setup rampung" --write-media /tmp/test_edge_tts.mp3

if [ -f /tmp/test_edge_tts.mp3 ]; then
    echo "✅ Setup berhasil! File tes ada di /tmp/test_edge_tts.mp3"
else
    echo "❌ Setup gagal, cek pesan error di atas"
    exit 1
fi
```

**Catatan:** script ini cuma menjalankan langkah yang sama dengan Bagian 2.1 secara berurutan + validasi tiap tahap — bukan solusi ajaib, tetap butuh Python sudah ada di sistem kamu. Kalau `pip install` gagal karena permission, coba jalankan dengan `pip install --user edge-tts` sebagai alternatif dari `--break-system-packages`.

---

## 3. Service: STT (ElevenLabs Scribe) — dipakai kedua fitur

`app/Services/Ai/SttService.php`

```php
<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class SttService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.api_key');
        $this->model = config('services.elevenlabs.stt_model');
    }

    /**
     * Transkripsi file audio ke teks.
     *
     * @param UploadedFile|string $audio  UploadedFile dari request, atau path file lokal
     * @param string $languageCode  Kode ISO 639-3, "jav" untuk Bahasa Jawa
     */
    public function transcribe(UploadedFile|string $audio, string $languageCode = 'jav'): array
    {
        $filePath = $audio instanceof UploadedFile ? $audio->getRealPath() : $audio;
        $fileName = $audio instanceof UploadedFile ? $audio->getClientOriginalName() : basename($audio);

        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->attach(
            'file', fopen($filePath, 'r'), $fileName
        )->post('https://api.elevenlabs.io/v1/speech-to-text', [
            'model_id' => $this->model,
            'language_code' => $languageCode,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('ElevenLabs STT gagal: ' . $response->body());
        }

        $data = $response->json();

        return [
            'text' => $data['text'] ?? '',
            'language_code' => $data['language_code'] ?? null,
            'language_probability' => $data['language_probability'] ?? null,
            'raw' => $data,
        ];
    }
}
```

**Catatan:**
- Kode bahasa Jawa di ElevenLabs itu `jav` (ISO 639-3), BUKAN `jv-ID` (format Azure/BCP-47) — jangan tertukar antara dua provider ini.
- Format audio yang didukung: mp3, wav, m4a, webm, ogg, flac, dll — hasil rekaman Flutter langsung bisa dipakai tanpa konversi.
- Free tier ElevenLabs terbatas (~2.5 jam audio/bulan) — pantau pemakaian kalau dipakai bareng-bareng untuk UAT internal.

---

## 4. Service: TTS (edge-tts) — dipakai kedua fitur

`app/Services/Ai/TtsService.php`

```php
<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TtsService
{
    protected string $binary;

    public function __construct()
    {
        $this->binary = config('services.edge_tts.binary');
    }

    /**
     * Generate audio dari teks, simpan ke storage, return path relatif.
     */
    public function synthesize(string $text, ?string $voice = null, string $disk = 'public'): string
    {
        $voice = $voice ?? config('services.edge_tts.voice_dimas');

        $fileName = 'tts/' . Str::uuid() . '.mp3';
        $absolutePath = Storage::disk($disk)->path($fileName);

        Storage::disk($disk)->makeDirectory('tts');

        $escapedText = escapeshellarg($text);
        $escapedVoice = escapeshellarg($voice);
        $escapedPath = escapeshellarg($absolutePath);

        $command = "{$this->binary} --voice {$escapedVoice} --text {$escapedText} --write-media {$escapedPath} 2>&1";

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($absolutePath)) {
            throw new RuntimeException('edge-tts gagal generate audio: ' . implode("\n", $output));
        }

        return $fileName; // path relatif di dalam disk, misal "tts/uuid.mp3"
    }
}
```

**Catatan penting soal `exec()`:**
- WAJIB Python + `edge-tts` terinstall di environment yang sama tempat Laravel jalan (server/laptop dev).
- Kalau nanti deploy ke **shared hosting**, cek dulu apakah `exec()` diizinkan (banyak shared hosting murah mem-block ini demi keamanan). Kalau diblokir, solusinya pindah provider (SpeechGen via HTTP API tidak butuh `exec()`) atau pakai VPS yang dikontrol penuh.
- Validasi & batasi panjang teks input sebelum masuk ke `exec()`, meskipun `escapeshellarg()` sudah menangani escaping dasar.

---

## 5. Quiz Suara: Fuzzy Matching (non-LLM)

`app/Services/Ai/PenilaianUcapanService.php`

```php
<?php

namespace App\Services\Ai;

class PenilaianUcapanService
{
    /**
     * Hitung skor kemiripan 0.0 - 1.0 antara hasil STT dan kunci jawaban.
     */
    public function hitungKemiripan(string $hasilStt, string $kunciJawaban): float
    {
        $a = strtolower(trim($hasilStt));
        $b = strtolower(trim($kunciJawaban));

        $jarak = levenshtein($a, $b);
        $panjangMax = max(strlen($a), strlen($b));

        return $panjangMax > 0 ? 1 - ($jarak / $panjangMax) : 0;
    }

    /**
     * Tentukan kategori hasil berdasarkan skor.
     * Threshold 0.85/0.6 adalah titik awal — sesuaikan setelah uji dengan sampel suara nyata.
     */
    public function kategorikan(float $skor): string
    {
        return match (true) {
            $skor >= 0.85 => 'benar',
            $skor >= 0.6 => 'hampir_benar',
            default => 'salah',
        };
    }
}
```

**Field tambahan yang perlu ada di `soal`** (atau di dalam `opsi_jawaban` JSON, sesuai keputusan PRD Bagian 6A yang membebaskan struktur JSON per `tipe_soal`) — diisi oleh guru saat membuat soal, bukan digenerate sistem:
- `respons_benar` — teks yang dibacakan TTS kalau kategori "benar"
- `respons_hampir_benar` — teks kalau "hampir_benar"
- `respons_salah` — teks kalau "salah" (bisa sertakan `kunci_jawaban` di dalamnya sebagai contoh yang benar)

**Threshold 0.85/0.6 wajib diuji ulang** dengan beberapa sampel rekaman suara sebelum dikunci — angka ini cuma titik awal, bukan hasil pengujian. Sejalan dengan saran PRD Bagian 9: sediakan toleransi skor, jangan pass/fail biner mutlak.

---

## 6. Latihan Ngomong: Feedback via RagService (Gemini 3.8 Flash)

Tidak perlu bikin service baru — panggil `RagService` yang sudah ada, dengan prompt khusus yang **selalu menyertakan kalimat referensi** sebagai pembanding:

```php
$prompt = "Kalimat referensi (yang seharusnya diucapkan siswa): \"{$soal->kunci_jawaban}\"\n"
        . "Hasil transkripsi ucapan siswa: \"{$hasilStt['text']}\"\n\n"
        . "Berikan feedback singkat (maksimal 2-3 kalimat) dalam Bahasa Jawa ngoko yang ramah, "
        . "tentang bagian mana yang kurang tepat (kata yang hilang, tertukar, atau kemungkinan pelafalan "
        . "yang kurang jelas berdasarkan perbedaan teks). Jika hasil transkripsi sudah sangat mendekati "
        . "kalimat referensi, beri pujian singkat saja tanpa mengarang kekurangan.";

$feedback = app(\App\Services\Ai\RagService::class)->ask(
    prompt: $prompt,
    mode: 'latihan_ngomong' // sesuaikan parameter dengan signature RagService yang sudah ada
);
```

**Penting:** cek dulu signature method `RagService` yang dipakai endpoint `POST /api/chat` — kemungkinan ada parameter guardrail/keyword-similarity yang perlu disesuaikan supaya tidak salah menolak prompt feedback ini (guardrail RAG chatbot awalnya didesain untuk chat bebas, bukan mode bandingkan-dua-teks seperti ini).

**Kenapa ini aman dipakai generatif (tidak seperti draf sebelumnya):** fitur ini **tidak masuk EXP/skor** — resiko Gemini salah menilai cuma berdampak ke kualitas feedback latihan, bukan ke penilaian resmi siswa.

---

## 7. Controller: Endpoint untuk Kedua Fitur

`app/Http/Controllers/Api/SpeakingExerciseController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Services\Ai\SttService;
use App\Services\Ai\TtsService;
use App\Services\Ai\RagService;
use App\Services\Ai\PenilaianUcapanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SpeakingExerciseController extends Controller
{
    /**
     * Quiz Suara — dinilai, masuk EXP. Non-LLM.
     */
    public function quizSuara(
        Request $request,
        Soal $soal,
        SttService $stt,
        TtsService $tts,
        PenilaianUcapanService $penilaian
    ) {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,webm,ogg|max:10240',
        ]);

        try {
            $hasilStt = $stt->transcribe($request->file('audio'), 'jav');

            $skor = $penilaian->hitungKemiripan($hasilStt['text'], $soal->kunci_jawaban);
            $kategori = $penilaian->kategorikan($skor);

            $teksRespons = match ($kategori) {
                'benar' => $soal->respons_benar ?? 'Pinter! Wis bener.',
                'hampir_benar' => $soal->respons_hampir_benar ?? 'Hampir bener, coba ulangi maneh.',
                'salah' => $soal->respons_salah ?? "Durung pas, iki jawaban sing bener: {$soal->kunci_jawaban}",
            };

            $audioPath = $tts->synthesize($teksRespons);

            // TODO: catat skor ke tabel exp/progres_siswa sesuai skema yang sudah ada

            return response()->json([
                'transkripsi' => $hasilStt['text'],
                'skor' => round($skor, 2),
                'kategori' => $kategori,
                'audio_url' => Storage::disk('public')->url($audioPath),
            ]);
        } catch (\Throwable $e) {
            Log::error('Quiz Suara gagal: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses audio, coba lagi.'], 500);
        }
    }

    /**
     * Latihan Ngomong — tidak dinilai, tanpa EXP. Pakai LLM.
     */
    public function latihanNgomong(
        Request $request,
        Soal $soal,
        SttService $stt,
        TtsService $tts,
        RagService $rag
    ) {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a,webm,ogg|max:10240',
        ]);

        try {
            $hasilStt = $stt->transcribe($request->file('audio'), 'jav');

            $prompt = "Kalimat referensi (yang seharusnya diucapkan siswa): \"{$soal->kunci_jawaban}\"\n"
                    . "Hasil transkripsi ucapan siswa: \"{$hasilStt['text']}\"\n\n"
                    . "Berikan feedback singkat (maksimal 2-3 kalimat) dalam Bahasa Jawa ngoko yang ramah, "
                    . "tentang bagian mana yang kurang tepat. Jika sudah sangat mendekati, beri pujian singkat saja.";

            $feedback = $rag->ask(prompt: $prompt);

            $audioPath = $tts->synthesize($feedback['text'] ?? $feedback);

            return response()->json([
                'transkripsi' => $hasilStt['text'],
                'feedback_text' => $feedback['text'] ?? $feedback,
                'audio_url' => Storage::disk('public')->url($audioPath),
            ]);
        } catch (\Throwable $e) {
            Log::error('Latihan Ngomong gagal: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses audio, coba lagi.'], 500);
        }
    }
}
```

Route (`routes/api.php`):
```php
Route::post('/soal/{soal}/quiz-suara', [SpeakingExerciseController::class, 'quizSuara']);
Route::post('/soal/{soal}/latihan-ngomong', [SpeakingExerciseController::class, 'latihanNgomong']);
```

---

## 8. Testing Manual (sebelum integrasi Flutter)

**Tes STT sendiri (curl):**
```bash
curl -X POST https://api.elevenlabs.io/v1/speech-to-text \
  -H "xi-api-key: $ELEVENLABS_API_KEY" \
  -F model_id="scribe_v2" \
  -F language_code="jav" \
  -F file=@rekaman_test.mp3
```

**Tes TTS sendiri (command line):**
```bash
edge-tts --voice jv-ID-DimasNeural --text "Sugeng enjing" --write-media hasil.mp3
```

**Tes endpoint Quiz Suara:**
```bash
curl -X POST http://localhost:8000/api/soal/1/quiz-suara \
  -F audio=@rekaman_test.mp3
```

**Tes endpoint Latihan Ngomong:**
```bash
curl -X POST http://localhost:8000/api/soal/1/latihan-ngomong \
  -F audio=@rekaman_test.mp3
```

Cek satu-satu dulu (STT saja → TTS saja → baru gabungan) supaya kalau ada yang gagal, gampang ketauan di tahap mana masalahnya.

---

## 9. Keterbatasan & Hal yang Perlu Dipantau

| Isu | Dampak | Mitigasi |
|---|---|---|
| edge-tts tidak resmi | Bisa berhenti berfungsi tanpa pemberitahuan | Arsitektur `TtsService` terpisah dari controller — gampang ganti provider (SpeechGen/Azure) kalau perlu |
| `exec()` di hosting | Bisa diblokir di shared hosting murah | Cek dulu kebijakan hosting sebelum deploy produksi; kalau perlu pindah ke VPS atau provider berbasis HTTP API |
| Free tier ElevenLabs (~2.5 jam/bulan) | Bisa habis kalau dipakai rame-rame saat UAT internal | Pantau pemakaian di dashboard ElevenLabs; upgrade tier kalau perlu |
| Latency pipeline (2-5 detik/putaran) | Bukan real-time, ada jeda tiap giliran | Kasih loading indicator jelas di Flutter selama proses record → respons |
| Akurasi STT Jawa (WER 10-20%, kategori "Good") | Transkripsi tidak selalu sempurna, terutama aksen non-baku | Threshold fuzzy-matching di Quiz Suara harus beri toleransi (lihat Bagian 5); untuk Latihan Ngomong dampaknya cuma feedback kurang presisi |
| Threshold fuzzy-matching (0.85/0.6) belum teruji | Bisa terlalu ketat/longgar untuk variasi ucapan siswa nyata | Uji dengan beberapa sampel rekaman sebelum dikunci sebagai nilai final |
| Guardrail RagService (Latihan Ngomong) | Awalnya didesain untuk chat bebas, bisa salah menolak prompt feedback | Cek/uji ulang keyword-similarity guardrail dengan contoh kalimat siswa sebelum dipakai di alur ini |

---

## 10. Ringkasan Environment yang Dibutuhkan

- [ ] Python 3.8+ terinstall di server dev
- [ ] `pip install edge-tts --break-system-packages` (manual, atau jalankan `setup-edge-tts.sh` di Bagian 2.4)
- [ ] Akun ElevenLabs + API key tersimpan di `.env`
- [ ] `config/services.php` sudah ditambah entri `elevenlabs` & `edge_tts`
- [ ] Storage disk `public` sudah di-link (`php artisan storage:link`)
- [ ] Proxy Gemini di `http://localhost:20128/v1` tetap jalan (dipakai bareng chatbot RAG, dipakai lagi untuk Latihan Ngomong)
- [ ] Field `respons_benar`/`respons_hampir_benar`/`respons_salah` sudah ditambah ke skema `soal`/`opsi_jawaban` untuk Quiz Suara
