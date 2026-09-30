<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoalResource;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MateriController extends Controller
{
    public function __construct(private readonly ProgresService $progres) {}

    /**
     * Daftar level materi berjenjang beserta status progres siswa (FR-2).
     */
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();

        $levels = LevelMateri::query()->withCount('soal')->orderBy('urutan')->get();
        $progres = ProgresSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('level_materi_id');

        return response()->json([
            'data' => $levels->map(fn (LevelMateri $level): array => [
                'id' => $level->id,
                'topik_id' => $level->topik_id,
                'nama_materi' => $level->nama_materi,
                'deskripsi' => $level->deskripsi,
                'reward_exp' => (int) $level->reward_exp,
                'urutan' => (int) $level->urutan,
                'jumlah_soal' => $level->soal_count,
                'status' => $progres[$level->id]->status ?? ProgresSiswa::STATUS_TERKUNCI,
                'tanggal_selesai' => $progres[$level->id]->tanggal_selesai ?? null,
            ])->values(),
        ]);
    }

    public function show(Request $request, LevelMateri $levelMateri): JsonResponse
    {
        $siswa = $request->user();

        $pembahasanId = $request->integer('pembahasan_id') ?: null;

        $soal = $levelMateri->soal()->orderBy('id');
        if ($pembahasanId) {
            $soal->where('pembahasan_id', $pembahasanId);
        }

        $soalCollection = $soal->get();
        $this->enrichStatusSoal($siswa, $soalCollection);

        return response()->json([
            'data' => $this->payload($siswa, $levelMateri, $pembahasanId, $soalCollection),
        ]);
    }

    /**
     * Mulai sesi quiz: blokir bila prasyarat belum tercapai (FR-2).
     * Sekaligus nuduhake soal pisanan sing durung tuntas (konsisten karo website).
     */
    public function mulai(Request $request, LevelMateri $levelMateri): JsonResponse
    {
        $siswa = $request->user();

        if (! $this->progres->canStart($siswa, $levelMateri)) {
            return response()->json([
                'message' => 'Materi belum tercapai. Selesaikan materi prasyarat terlebih dahulu.',
            ], 403);
        }

        $pembahasanId = $request->integer('pembahasan_id') ?: null;

        $soal = $levelMateri->soal()->orderBy('id');
        if ($pembahasanId) {
            $soal->where('pembahasan_id', $pembahasanId);
        }

        $soalCollection = $soal->get();
        $this->enrichStatusSoal($siswa, $soalCollection);

        // Soal pertama sing durung lulus ing scope iki; yen kabeh wis lulus,
        // golek ing scope level (kabeh bagian) sadurunge bali menyang soal pisanan.
        $firstUnfinished = $this->firstUnfinished($soalCollection);

        if (! $firstUnfinished && $pembahasanId) {
            $levelSoal = $levelMateri->soal()->orderBy('id')->get();
            $this->enrichStatusSoal($siswa, $levelSoal);
            $firstUnfinished = $this->firstUnfinished($levelSoal);
        }

        return response()->json([
            'message' => 'Sesi quiz siap dimulai.',
            'data' => $this->payload($siswa, $levelMateri, $pembahasanId, $soalCollection)
                + ['first_unfinished_soal_id' => ($firstUnfinished ?? $soalCollection->first())?->id],
        ]);
    }

    /**
     * Isi atribut dinamis `skor_tertinggi_siswa` ing saben soal supaya
     * SoalResource bisa nuduhake status pengerjaan siswa.
     *
     * @param  Collection<int, \App\Models\Soal>  $soal
     */
    private function enrichStatusSoal(Siswa $siswa, Collection $soal): void
    {
        if ($soal->isEmpty()) {
            return;
        }

        $skorMap = JawabanSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->whereIn('soal_id', $soal->pluck('id'))
            ->pluck('skor_tertinggi', 'soal_id');

        foreach ($soal as $item) {
            $item->setAttribute('skor_tertinggi_siswa', $skorMap[$item->id] ?? null);
        }
    }

    /**
     * @param  Collection<int, \App\Models\Soal>  $soal
     */
    private function firstUnfinished(Collection $soal): ?\App\Models\Soal
    {
        return $soal->first(function ($item): bool {
            $skor = $item->skor_tertinggi_siswa;

            return $skor === null || (int) $skor < QuizScoringService::PASS_THRESHOLD;
        });
    }

    /**
     * @param  Collection<int, \App\Models\Soal>  $soalCollection
     * @return array<string, mixed>
     */
    private function payload(Siswa $siswa, LevelMateri $levelMateri, ?int $pembahasanId, Collection $soalCollection): array
    {
        return [
            'level_materi' => $levelMateri->only(['id', 'topik_id', 'nama_materi', 'deskripsi', 'reward_exp', 'urutan']),
            'pembahasan_id' => $pembahasanId,
            'status' => $this->progres->statusFor($siswa, $levelMateri),
            'soal' => SoalResource::collection($soalCollection),
        ];
    }
}
