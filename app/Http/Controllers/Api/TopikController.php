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

        $allUnitIds = $topikList->pluck('units')->collapse()->pluck('id')->unique()->values();
        $soalByUnit = Soal::query()
            ->whereIn('level_materi_id', $allUnitIds)
            ->get(['id', 'level_materi_id'])
            ->groupBy('level_materi_id');
        $unitSelesaiIds = ProgresSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->where('status', ProgresSiswa::STATUS_SELESAI)
            ->whereIn('level_materi_id', $allUnitIds)
            ->pluck('level_materi_id');

        $data = $topikList->map(function (Topik $topik) use ($lulusIds, $soalByUnit, $unitSelesaiIds): array {
            $unitIds = $topik->units->pluck('id');
            $soalIds = $unitIds->flatMap(fn ($unitId) => ($soalByUnit->get($unitId) ?? collect())->pluck('id'));
            $totalSoal = $soalIds->count();
            $lulus = $lulusIds->intersect($soalIds)->count();
            $persen = $totalSoal > 0 ? (int) round(($lulus / $totalSoal) * 100) : 0;

            $unitSelesai = $unitIds->intersect($unitSelesaiIds)->count();

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

        $unitIds = $units->pluck('id');
        $soalByLevel = Soal::query()
            ->whereIn('level_materi_id', $unitIds)
            ->get(['id', 'level_materi_id', 'pembahasan_id'])
            ->groupBy('level_materi_id');
        $pembahasanPerLevel = Pembahasan::query()
            ->whereIn('level_materi_id', $unitIds)
            ->withCount('soal')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->groupBy('level_materi_id');
        $soalByPembahasan = Soal::query()
            ->whereIn('pembahasan_id', $pembahasanPerLevel->collapse()->pluck('id'))
            ->get(['id', 'pembahasan_id'])
            ->groupBy('pembahasan_id');

        $unitData = $units->values()->map(function (LevelMateri $level, int $index) use ($progresMap, $lulusIds, $soalByLevel, $pembahasanPerLevel, $soalByPembahasan): array {
            $status = $progresMap[$level->id]->status ?? ProgresSiswa::STATUS_TERKUNCI;

            $levelSoalIds = ($soalByLevel->get($level->id) ?? collect())->pluck('id');
            $lulusLevel = $levelSoalIds->filter(fn ($id) => $lulusIds->has($id))->count();
            $totalLevel = $level->soal_count;
            $persenLevel = $totalLevel > 0 ? (int) round(($lulusLevel / $totalLevel) * 100) : 0;

            $terkunci = $status === ProgresSiswa::STATUS_TERKUNCI;

            $bagian = ($pembahasanPerLevel->get($level->id) ?? collect())
                ->map(function (Pembahasan $pembahasan) use ($lulusIds, $terkunci, $soalByPembahasan): array {
                    $soalIds = ($soalByPembahasan->get($pembahasan->id) ?? collect())->pluck('id');
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
