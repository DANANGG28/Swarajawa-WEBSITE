<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoalResource;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Services\ProgresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json([
            'data' => [
                'level_materi' => $levelMateri->only(['id', 'nama_materi', 'deskripsi', 'reward_exp', 'urutan']),
                'status' => $this->progres->statusFor($siswa, $levelMateri),
                'soal' => SoalResource::collection(
                    $levelMateri->soal()->orderBy('id')->get()
                ),
            ],
        ]);
    }

    /**
     * Mulai sesi quiz: blokir bila prasyarat belum tercapai (FR-2).
     */
    public function mulai(Request $request, LevelMateri $levelMateri): JsonResponse
    {
        $siswa = $request->user();

        if (! $this->progres->canStart($siswa, $levelMateri)) {
            return response()->json([
                'message' => 'Materi belum tercapai. Selesaikan materi prasyarat terlebih dahulu.',
            ], 403);
        }

        return response()->json([
            'message' => 'Sesi quiz siap dimulai.',
            'level_materi' => $levelMateri->only(['id', 'nama_materi', 'deskripsi', 'reward_exp']),
            'soal' => SoalResource::collection(
                $levelMateri->soal()->orderBy('id')->get()
            ),
        ]);
    }
}
