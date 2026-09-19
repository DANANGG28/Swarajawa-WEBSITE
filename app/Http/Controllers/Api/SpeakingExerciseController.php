<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Services\Ai\PenilaianUcapanService;
use App\Services\Ai\RagService;
use App\Services\Ai\SttService;
use App\Services\Ai\TtsService;
use App\Services\JawabanService;
use App\Services\ProgresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Dua fitur speaking practice (setup-tts-stt-live.md):
 *
 * - Quiz Suara      → dinilai, masuk EXP, fuzzy matching non-LLM (FR-7/FR-8).
 * - Latihan Ngomong → tidak dinilai, feedback kualitatif via RagService (Gemini).
 */
class SpeakingExerciseController extends Controller
{
    public function __construct(
        private readonly SttService $stt,
        private readonly TtsService $tts,
        private readonly PenilaianUcapanService $penilaian,
        private readonly JawabanService $jawaban,
        private readonly ProgresService $progres,
        private readonly RagService $rag,
    ) {}

    /**
     * Quiz Suara — dinilai, masuk EXP. Non-LLM (PRD §6C).
     */
    public function quizSuara(Request $request, Soal $soal): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,webm,ogg,flac', 'max:10240'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($soal->tipe_soal !== Soal::TIPE_KUIS_SUARA) {
            return response()->json(['message' => 'Soal ini bukan tipe kuis suara.'], 422);
        }

        $siswa = $request->user();

        if ($soal->levelMateri && ! $this->progres->canStart($siswa, $soal->levelMateri)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        try {
            $referensi = $this->teksReferensi($soal);

            $hasilStt = $this->stt->transcribeFile($data['audio'], 'jav', $data['mock_transcript'] ?? null);

            $kemiripan = $this->penilaian->hitungKemiripan((string) $hasilStt['text'], $referensi);
            $kategori = $this->penilaian->kategorikan($kemiripan);
            $skor = (int) round($kemiripan * 100);

            $teksRespons = $this->teksRespons($soal, $kategori, $referensi);
            $tts = $this->tts->synthesize($teksRespons);

            $catatan = $this->jawaban->record($siswa, $soal, [
                'benar' => $kategori === PenilaianUcapanService::BENAR,
                'skor' => $skor,
                'detail' => [
                    'transcript' => $hasilStt['text'],
                    'kunci' => $referensi,
                    'kategori' => $kategori,
                ],
            ]);

            return response()->json([
                'mock' => (bool) ($hasilStt['mock'] || $tts['mock']),
                'transkripsi' => $hasilStt['text'],
                'skor' => $skor,
                'kategori' => $kategori,
                'benar' => $kategori === PenilaianUcapanService::BENAR,
                'teks_respons' => $teksRespons,
                'audio_url' => $tts['audio_url'],
                'skor_tertinggi' => $catatan['skor_tertinggi'],
                'exp_didapat' => $catatan['exp_didapat'],
                'reward_exp' => $catatan['reward_exp'],
                'level_selesai' => $catatan['level_selesai'],
                'level_berikutnya' => $catatan['level_berikutnya'],
                'total_exp' => $catatan['total_exp'],
                'current_streak' => $catatan['current_streak'],
                'highest_streak' => $catatan['highest_streak'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Quiz Suara gagal: '.$e->getMessage());

            return response()->json(['message' => 'Gagal memproses audio, coba lagi.'], 500);
        }
    }

    /**
     * Latihan Ngomong — tidak dinilai, tanpa EXP. Pakai LLM (RagService).
     */
    public function latihanNgomong(Request $request, Soal $soal): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,webm,ogg,flac', 'max:10240'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        $siswa = $request->user();

        if ($soal->levelMateri && ! $this->progres->canStart($siswa, $soal->levelMateri)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        try {
            $referensi = $this->teksReferensi($soal);

            $hasilStt = $this->stt->transcribeFile($data['audio'], 'jav', $data['mock_transcript'] ?? null);

            $prompt = "Kalimat referensi (yang seharusnya diucapkan siswa): \"{$referensi}\"\n"
                ."Hasil transkripsi ucapan siswa: \"{$hasilStt['text']}\"\n\n"
                .'Berikan feedback singkat (maksimal 2-3 kalimat) dalam Bahasa Jawa ngoko yang ramah, '
                .'tentang bagian mana yang kurang tepat (kata yang hilang, tertukar, atau kemungkinan pelafalan '
                .'yang kurang jelas berdasarkan perbedaan teks). Jika hasil transkripsi sudah sangat mendekati '
                .'kalimat referensi, beri pujian singkat saja tanpa mengarang kekurangan. '
                .'Wangsulana mung teks feedback wae, tanpa pambuka utawa panutup tambahan.';

            $feedback = $this->rag->askDirect($prompt)
                ?? 'Nyuwun pangapunten, kula dereng saged paring pamrayoga. Sumangga dipuncobi malih.';

            $tts = $this->tts->synthesize($feedback);

            return response()->json([
                'mock' => (bool) ($hasilStt['mock'] || $tts['mock']),
                'transkripsi' => $hasilStt['text'],
                'feedback_text' => $feedback,
                'audio_url' => $tts['audio_url'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Latihan Ngomong gagal: '.$e->getMessage());

            return response()->json(['message' => 'Gagal memproses audio, coba lagi.'], 500);
        }
    }

    private function teksReferensi(Soal $soal): string
    {
        $kunci = $soal->kunci_jawaban ?? [];

        return (string) ($kunci['teks'] ?? $kunci['jawaban'] ?? '');
    }

    private function teksRespons(Soal $soal, string $kategori, string $referensi): string
    {
        $opsi = $soal->opsi_jawaban ?? [];

        return match ($kategori) {
            PenilaianUcapanService::BENAR => (string) ($opsi['respons_benar'] ?? 'Pinter! Pangucapanmu wis bener.'),
            PenilaianUcapanService::HAMPIR_BENAR => (string) ($opsi['respons_hampir_benar'] ?? 'Hampir bener, coba dibaleni maneh ya.'),
            default => (string) ($opsi['respons_salah'] ?? "Durung pas. Sing bener: \"{$referensi}\". Ayo dicoba maneh."),
        };
    }
}
