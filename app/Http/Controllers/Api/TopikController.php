<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Soal;
use App\Models\Topik;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Hierarki kurikulum untuk siswa: Topik -> Unit (level_materi) -> Bagian (pembahasan) -> Soal.
 */
class TopikController extends Controller
{
    public function __construct(private readonly ProgresService $progres) {}

    /**
     * Daftar topik beserta ringkasan progres (untuk kartu pilih-topik).
     */
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();

        if ($siswa->progres()->count() === 0) {
            $this->progres->initialize($siswa);
        }

        $lulusIds = JawabanSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id');

        $topikList = Topik::query()->with('units')->orderBy('urutan')->get();

        $data = $topikList->map(function (Topik $topik) use ($siswa, $lulusIds): array {
            $unitIds = $topik->units->pluck('id');
            $soalIds = Soal::query()->whereIn('level_materi_id', $unitIds)->pluck('id');
            $totalSoal = $soalIds->count();
            $lulus = $lulusIds->intersect($soalIds)->count();
            $persen = $totalSoal > 0 ? (int) round(($lulus / $totalSoal) * 100) : 0;

            $unitSelesai = ProgresSiswa::query()
                ->where('siswa_id', $siswa->id)
                ->whereIn('level_materi_id', $unitIds)
                ->where('status', ProgresSiswa::STATUS_SELESAI)
                ->count();

            $status = ($totalSoal > 0 && $lulus >= $totalSoal)
                ? 'selesai'
                : ($lulus > 0 ? 'berjalan' : 'anyar');

            return [
                'id' => $topik->id,
                'nama' => $topik->nama,
                'deskripsi' => $topik->deskripsi,
                'urutan' => (int) $topik->urutan,
                'total_unit' => $unitIds->count(),
                'unit_selesai' => $unitSelesai,
                'total_soal' => $totalSoal,
                'lulus_count' => $lulus,
                'persen' => $persen,
                'status' => $status,
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Detail satu topik: unit-unit beserta bagian (pembahasan) di dalamnya.
     */
    public function show(Request $request, Topik $topik): JsonResponse
    {
        $siswa = $request->user();

        if ($siswa->progres()->count() === 0) {
            $this->progres->initialize($siswa);
        }

        $progresMap = ProgresSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('level_materi_id');

        $lulusIds = JawabanSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id')
            ->flip();

        $units = LevelMateri::query()
            ->where('topik_id', $topik->id)
            ->withCount(['soal', 'pembahasan'])
            ->orderBy('urutan')
            ->get();

        $unitData = $units->values()->map(function (LevelMateri $level, int $index) use ($progresMap, $lulusIds): array {
            $status = $progresMap[$level->id]->status ?? ProgresSiswa::STATUS_TERKUNCI;

            $levelSoalIds = $level->soal()->pluck('id');
            $lulusLevel = $levelSoalIds->filter(fn ($id) => $lulusIds->has($id))->count();
            $totalLevel = $level->soal_count;
            $persenLevel = $totalLevel > 0 ? (int) round(($lulusLevel / $totalLevel) * 100) : 0;

            $terkunci = $status === ProgresSiswa::STATUS_TERKUNCI;

            $bagian = Pembahasan::query()
                ->where('level_materi_id', $level->id)
                ->withCount('soal')
                ->orderBy('urutan')
                ->get()
                ->map(function (Pembahasan $pembahasan) use ($lulusIds, $terkunci): array {
                    $soalIds = $pembahasan->soal()->pluck('id');
                    $lulus = $soalIds->filter(fn ($id) => $lulusIds->has($id))->count();
                    $total = $pembahasan->soal_count;
                    $persen = $total > 0 ? (int) round(($lulus / $total) * 100) : 0;

                    $statusBagian = ($total > 0 && $lulus >= $total)
                        ? 'selesai'
                        : ($lulus > 0 ? 'berjalan' : 'anyar');

                    return [
                        'id' => $pembahasan->id,
                        'nama' => $pembahasan->nama,
                        'deskripsi' => $pembahasan->deskripsi,
                        'urutan' => (int) $pembahasan->urutan,
                        'jumlah_soal' => $total,
                        'lulus_count' => $lulus,
                        'persen' => $persen,
                        'status' => $statusBagian,
                        'terkunci' => $terkunci,
                    ];
                })->values();

            return [
                'id' => $level->id,
                'topik_id' => $level->topik_id,
                'nama_materi' => $level->nama_materi,
                'deskripsi' => $level->deskripsi,
                'urutan' => (int) $level->urutan,
                'urutan_unit' => $index + 1,
                'reward_exp' => (int) $level->reward_exp,
                'jumlah_soal' => $totalLevel,
                'lulus_count' => $lulusLevel,
                'persen' => $persenLevel,
                'jumlah_pembahasan' => $level->pembahasan_count,
                'status' => $status,
                'pembahasan' => $bagian,
            ];
        });

        return response()->json([
            'data' => [
                'topik' => [
                    'id' => $topik->id,
                    'nama' => $topik->nama,
                    'deskripsi' => $topik->deskripsi,
                    'urutan' => (int) $topik->urutan,
                ],
                'units' => $unitData,
            ],
        ]);
    }
}
