<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Services\Ai\RagService;
use App\Services\Ai\StsService;
use App\Services\Ai\SttService;
use App\Services\Ai\TtsService;
use App\Services\GamificationService;
use App\Services\JawabanService;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Sesi kuis web (Blade) — memakai ulang service yang sama dengan REST API.
 * Lima layar: FR-3, FR-4, FR-8 (STS), FR-7 (STT), FR-22 (tracing).
 */
class KuisSesiController extends Controller
{
    public function __construct(
        private readonly QuizScoringService $scoring,
        private readonly GamificationService $gamification,
        private readonly TtsService $tts,
        private readonly SttService $stt,
        private readonly StsService $sts,
        private readonly RagService $rag,
        private readonly ProgresService $progres,
        private readonly JawabanService $jawaban,
    ) {}

    public function pilihanGanda(): View
    {
        return $this->render('kuis.pilihan-ganda', Soal::TIPE_PILIHAN_GANDA, 'Pilihan Ganda');
    }

    public function susunUkara(): View
    {
        return $this->render('kuis.susun-ukara', Soal::TIPE_SUSUN_KALIMAT, 'Susun Ukara');
    }

    public function wicaraAudio(): View
    {
        return $this->render('kuis.wicara-audio', Soal::TIPE_KUIS_SUARA, 'Kuis Wicara (STS)');
    }

    public function speakToText(): View
    {
        return $this->render('kuis.speak-to-text', Soal::TIPE_KUIS_SUARA, 'Latihan Speak to Text');
    }

    public function tracingAksara(): View
    {
        return $this->render('kuis.tracing-aksara', Soal::TIPE_MENULIS_AKSARA, 'Tracing Aksara');
    }

    /**
     * Mulai level: arahkan ke soal pertama yang belum tuntas ing level kasebut.
     * Otomatis nuduhake tipe kuis sing bener liwat urlForSoal().
     */
    public function mulaiLevel(LevelMateri $levelMateri): RedirectResponse
    {
        $siswa = $this->siswa();

        if (! $this->progres->canStart($siswa, $levelMateri)) {
            return redirect()->route('siswa.latihan')
                ->with('error', 'Materi belum tercapai. Rampungake level sadurunge dhisik.');
        }

        $soal = $this->jawaban->firstUnfinishedInLevel($siswa, $levelMateri)
            ?? Soal::where('level_materi_id', $levelMateri->id)->orderBy('id')->first();

        if (! $soal) {
            return redirect()->route('siswa.latihan')
                ->with('error', 'Durung ana soal ing level iki.');
        }

        return redirect()->to($this->urlForSoal($soal));
    }

    /**
     * Nilai jawaban (semua tipe) + catat EXP & streak.
     * EXP dihitung bertingkat: hanya selisih jika skor baru melebihi rekor sebelumnya.
     */
    public function jawab(Request $request): JsonResponse
    {
        $data = $request->validate([
            'soal_id' => ['required', 'integer', 'exists:soal,id'],
            'jawaban' => ['present'],
        ]);

        $soal = Soal::query()->with('levelMateri')->findOrFail($data['soal_id']);
        $siswa = $this->siswa();

        // FR-2: blokir bila level masih terkunci.
        if ($soal->levelMateri && ! $this->progres->canStart($siswa, $soal->levelMateri)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        $hasil = $this->scoring->score($soal, $data['jawaban']);
        $catatan = $this->jawaban->record($siswa, $soal, $hasil);

        $nextSoal = $this->jawaban->nextSoal($siswa, $soal->levelMateri, $soal->id);

        return response()->json([
            'benar' => $hasil['benar'],
            'skor' => $hasil['skor'],
            'skor_tertinggi' => $catatan['skor_tertinggi'],
            'detail' => $hasil['detail'],
            'kunci_jawaban' => $soal->kunci_jawaban,
            'exp_didapat' => $catatan['exp_didapat'],
            'reward_exp' => $catatan['reward_exp'],
            'level_selesai' => $catatan['level_selesai'],
            'level_berikutnya' => $catatan['level_berikutnya'],
            'next_soal_id' => $nextSoal?->id,
            'next_url' => $nextSoal ? $this->urlForSoal($nextSoal) : null,
            'total_exp' => $catatan['total_exp'],
            'current_streak' => $catatan['current_streak'],
            'highest_streak' => $catatan['highest_streak'],
        ]);
    }

    public function tts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teks' => ['required', 'string', 'max:1000'],
            'voice' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json($this->tts->synthesize($data['teks'], $data['voice'] ?? null));
    }

    public function stt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'string'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json($this->stt->transcribe($data['audio'], mockTranscript: $data['mock_transcript'] ?? null));
    }

    public function sts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'string'],
            'teks_referensi' => ['required', 'string', 'max:1000'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json($this->sts->respond($data['audio'], $data['teks_referensi'], $data['mock_transcript'] ?? null));
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:1000'],
            'session_id' => ['nullable', 'integer'],
        ]);

        $siswa = $this->siswa();
        $session = null;

        if (! empty($data['session_id'])) {
            $session = $siswa->chatSessions()->find($data['session_id']);
        }

        if (! $session) {
            $session = $siswa->chatSessions()->create([
                'judul' => Str::limit($data['pertanyaan'], 45),
            ]);
        }

        $session->messages()->create([
            'role' => 'user',
            'pesan' => $data['pertanyaan'],
        ]);

        $result = $this->rag->ask($data['pertanyaan']);

        $session->messages()->create([
            'role' => 'assistant',
            'pesan' => $result['jawaban'],
            'sumber' => $result['sumber'] ?? [],
        ]);

        $session->touch();

        return response()->json(array_merge($result, [
            'session_id' => $session->id,
            'session_title' => $session->judul,
        ]));
    }

    public function chatHistori(): JsonResponse
    {
        $sessions = $this->siswa()->chatSessions()
            ->latest('updated_at')
            ->take(20)
            ->get(['id', 'judul', 'updated_at']);

        return response()->json($sessions);
    }

    public function chatSesiShow(ChatSession $chatSession): JsonResponse
    {
        if ($chatSession->siswa_id !== $this->siswa()->id) {
            abort(403, 'Akses ditolak.');
        }

        $chatSession->load('messages');

        return response()->json([
            'id' => $chatSession->id,
            'judul' => $chatSession->judul,
            'messages' => $chatSession->messages->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'role' => $m->role,
                'pesan' => $m->pesan,
                'sumber' => $m->sumber,
                'waktu' => $m->created_at->format('H.i').' WIB',
            ]),
        ]);
    }

    public function chatSesiDestroy(ChatSession $chatSession): JsonResponse
    {
        if ($chatSession->siswa_id !== $this->siswa()->id) {
            abort(403, 'Akses ditolak.');
        }

        $chatSession->delete();

        return response()->json(['status' => 'ok']);
    }

    private function render(string $view, string $tipe, string $judul): View
    {
        $siswa = $this->siswa();
        $this->gamification->syncStreak($siswa);
        $accessibleLevelIds = $this->levelIds($siswa->id);

        $query = Soal::query()
            ->with('levelMateri')
            ->whereIn('level_materi_id', $accessibleLevelIds)
            ->where('tipe_soal', $tipe);

        if (request()->filled('level_materi_id')) {
            $levelId = (int) request('level_materi_id');
            if ($accessibleLevelIds->contains($levelId)) {
                $query->where('level_materi_id', $levelId);
            }
        }

        $total = (clone $query)->count();
        $soal = null;

        if (request()->filled('soal_id')) {
            $requestedId = (int) request('soal_id');
            $soal = (clone $query)->where('id', $requestedId)->first();
        }

        if (! $soal) {
            $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
                ->where('skor_tertinggi', '>=', 100)
                ->pluck('soal_id');

            $soal = (clone $query)
                ->whereNotIn('id', $selesaiSoalIds)
                ->orderBy('level_materi_id')
                ->orderBy('id')
                ->first();

            if (! $soal) {
                $soal = (clone $query)
                    ->orderBy('level_materi_id')
                    ->orderBy('id')
                    ->first();
            }
        }

        $nomor = $soal ? (clone $query)->where('id', '<=', $soal->id)->count() : 0;

        return view($view, [
            'judul' => $judul,
            'header' => [
                'nama' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas,
                'total_exp' => (int) ($siswa->exp()->value('total_exp') ?? 0),
                'streak' => (int) ($siswa->strek()->value('current_streak') ?? 0),
            ],
            'progress' => ['nomor' => $nomor, 'total' => $total],
            'soal' => $soal ? $this->payload($soal) : null,
        ]);
    }

    public function urlForSoal(Soal $soal): string
    {
        $params = ['soal_id' => $soal->id, 'level_materi_id' => $soal->level_materi_id];

        return match ($soal->tipe_soal) {
            Soal::TIPE_PILIHAN_GANDA => route('kuis.pilihan-ganda', $params),
            Soal::TIPE_SUSUN_KALIMAT => route('kuis.susun-ukara', $params),
            Soal::TIPE_MENULIS_AKSARA => route('kuis.tracing-aksara', $params),
            Soal::TIPE_KUIS_SUARA => route('kuis.wicara-audio', $params),
            default => route('kuis.pilihan-ganda', $params),
        };
    }

    /**
     * @return Collection<int, int>
     */
    private function levelIds(int $siswaId)
    {
        return ProgresSiswa::query()
            ->where('siswa_id', $siswaId)
            ->where('status', '!=', ProgresSiswa::STATUS_TERKUNCI)
            ->pluck('level_materi_id');
    }

    /**
     * Payload soal yang aman untuk klien (tanpa kunci jawaban objektif).
     *
     * @return array<string, mixed>
     */
    private function payload(Soal $soal): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $opsi = $soal->opsi_jawaban ?? [];

        return [
            'id' => $soal->id,
            'tipe' => $soal->tipe_soal,
            'pertanyaan' => $soal->pertanyaan,
            'bobot_exp' => (int) $soal->bobot_exp,
            'media_audio_url' => $soal->media_audio_url,
            'level' => [
                'id' => $soal->levelMateri?->id,
                'nama' => $soal->levelMateri?->nama_materi,
                'urutan' => $soal->levelMateri?->urutan,
            ],
            'opsi' => $opsi,
            'teks_referensi' => $soal->tipe_soal === Soal::TIPE_KUIS_SUARA ? ($kunci['teks'] ?? null) : null,
            'aksara' => is_array($opsi) ? ($opsi['aksara'] ?? null) : null,
            'petunjuk' => is_array($opsi) ? ($opsi['petunjuk'] ?? null) : null,
            'paths' => $soal->tipe_soal === Soal::TIPE_MENULIS_AKSARA ? ($kunci['paths'] ?? null) : null,
        ];
    }

    private function siswa(): Siswa
    {
        /** @var Siswa $siswa */
        $siswa = Auth::guard('siswa')->user();

        return $siswa;
    }
}
