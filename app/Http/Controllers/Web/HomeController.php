<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Services\GamificationService;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use App\Support\AuthContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
        private readonly ProgresService $progres,
    ) {}

    /**
     * Beranda: tamu diarahkan ke halaman login, siswa melihat halaman
     * pembelajaran dinamis, guru/superadmin diarahkan ke dashboard perannya.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $role = AuthContext::roleOf(AuthContext::currentUser($request));

        if ($role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        if ($role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($role === 'siswa') {
            /** @var Siswa $siswa */
            $siswa = AuthContext::currentUser($request);

            $this->gamification->syncStreak($siswa);

            // Inisialisasi progres jika belum ada
            if ($siswa->progres()->count() === 0) {
                $this->progres->initialize($siswa);
            }

            $totalExp = (int) ($siswa->exp()->value('total_exp') ?? 0);
            $strek = $siswa->strek()->first();
            $currentStreak = (int) ($strek?->current_streak ?? 0);
            $highestStreak = (int) ($strek?->highest_streak ?? 0);

            // Level & progres
            $levels = LevelMateri::query()
                ->withCount('soal')
                ->orderBy('urutan')
                ->get()
                ->map(function (LevelMateri $level) use ($siswa) {
                    $progres = ProgresSiswa::where('siswa_id', $siswa->id)
                        ->where('level_materi_id', $level->id)
                        ->first();

                    $levelSoalIds = $level->soal()->pluck('id');
                    $jawabanLevel = JawabanSiswa::where('siswa_id', $siswa->id)
                        ->whereIn('soal_id', $levelSoalIds)
                        ->get();

                    $lulusCount = $jawabanLevel->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
                    $totalSoal = $level->soal_count;
                    $persen = $totalSoal > 0 ? (int) round(($lulusCount / $totalSoal) * 100) : 0;
                    $rataSkor = $jawabanLevel->count() > 0 ? (int) round($jawabanLevel->avg('skor_tertinggi')) : 0;

                    $status = $progres?->status ?? ProgresSiswa::STATUS_TERKUNCI;

                    return (object) [
                        'id' => $level->id,
                        'urutan' => $level->urutan,
                        'nama_materi' => $level->nama_materi,
                        'deskripsi' => $level->deskripsi,
                        'reward_exp' => $level->reward_exp,
                        'total_soal' => $totalSoal,
                        'lulus_count' => $lulusCount,
                        'persen' => $persen,
                        'rata_skor' => $rataSkor,
                        'status' => $status,
                    ];
                });

            $activeLevel = $levels->firstWhere('status', ProgresSiswa::STATUS_BERJALAN)
                ?? $levels->firstWhere('status', ProgresSiswa::STATUS_SELESAI)
                ?? $levels->first();

            $accessibleLevelIds = $levels->where('status', '!=', ProgresSiswa::STATUS_TERKUNCI)->pluck('id');

            // Aksara tracing mastery (FR-22)
            $totalAksara = Soal::where('tipe_soal', Soal::TIPE_MENULIS_AKSARA)->count();
            $aksaraDikuasai = JawabanSiswa::where('siswa_id', $siswa->id)
                ->whereHas('soal', fn ($q) => $q->where('tipe_soal', Soal::TIPE_MENULIS_AKSARA))
                ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
                ->count();
            $aksaraPersen = $totalAksara > 0 ? (int) round(($aksaraDikuasai / $totalAksara) * 100) : 0;

            // Tantangan hari ini (soal aktif yang belum 100%)
            $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
                ->where('skor_tertinggi', '>=', 100)
                ->pluck('soal_id');

            $tantangan = Soal::with('levelMateri')
                ->whereIn('level_materi_id', $accessibleLevelIds)
                ->whereNotIn('id', $selesaiSoalIds)
                ->first()
                ?? Soal::with('levelMateri')->whereIn('level_materi_id', $accessibleLevelIds)->first();

            // Aktivitas kuis terakhir
            $lastJawaban = JawabanSiswa::with('soal.levelMateri')
                ->where('siswa_id', $siswa->id)
                ->latest('updated_at')
                ->first();

            $recentLevel = $lastJawaban?->soal?->levelMateri ?? ($activeLevel ? LevelMateri::find($activeLevel->id) : null);
            $recentProgress = null;
            if ($recentLevel) {
                $recentTotalSoal = Soal::where('level_materi_id', $recentLevel->id)->count();
                $recentSoalIds = Soal::where('level_materi_id', $recentLevel->id)->pluck('id');
                $recentSelesai = JawabanSiswa::where('siswa_id', $siswa->id)
                    ->whereIn('soal_id', $recentSoalIds)
                    ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
                    ->count();

                $recentPersen = $recentTotalSoal > 0 ? (int) round(($recentSelesai / $recentTotalSoal) * 100) : 0;

                $recentProgress = [
                    'nama_materi' => $recentLevel->nama_materi,
                    'total_soal' => $recentTotalSoal,
                    'selesai_soal' => $recentSelesai,
                    'persen' => $recentPersen,
                    'level_id' => $recentLevel->id,
                ];
            }

            return view('welcome', [
                'siswa' => $siswa,
                'totalExp' => $totalExp,
                'currentStreak' => $currentStreak,
                'highestStreak' => $highestStreak,
                'levels' => $levels,
                'activeLevel' => $activeLevel,
                'totalAksara' => $totalAksara,
                'aksaraDikuasai' => $aksaraDikuasai,
                'aksaraPersen' => $aksaraPersen,
                'tantangan' => $tantangan,
                'recentProgress' => $recentProgress,
            ]);
        }

        return redirect()->route('masuk');
    }

    /**
     * Halaman latihan soal & asesmen dinamis — menghubungkan soal guru ke siswa.
     */
    public function latihanSoal(Request $request): View
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);
        $this->gamification->syncStreak($siswa);

        if ($siswa->progres()->count() === 0) {
            $this->progres->initialize($siswa);
        }

        $levels = LevelMateri::query()->withCount('soal')->orderBy('urutan')->get();
        $progresMap = ProgresSiswa::where('siswa_id', $siswa->id)->get()->keyBy('level_materi_id');
        $accessibleLevelIds = $progresMap->where('status', '!=', ProgresSiswa::STATUS_TERKUNCI)->pluck('level_materi_id');

        $selectedLevelId = $request->filled('level_materi_id') ? (int) $request->level_materi_id : null;

        $query = Soal::query()->with('levelMateri');

        if ($selectedLevelId) {
            $query->where('level_materi_id', $selectedLevelId);
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', $request->tipe_soal);
        }

        if ($request->filled('q')) {
            $query->where('pertanyaan', 'like', '%' . $request->q . '%');
        }

        $soalPaginator = $query->orderBy('level_materi_id')->orderBy('id')->paginate(10)->withQueryString();

        $jawabanMap = JawabanSiswa::where('siswa_id', $siswa->id)->get()->keyBy('soal_id');
        $kuisCtrl = app(KuisSesiController::class);

        $soalList = $soalPaginator->through(function (Soal $soal) use ($jawabanMap, $progresMap, $kuisCtrl) {
            $jwb = $jawabanMap->get($soal->id);
            $progresLevel = $progresMap->get($soal->level_materi_id);
            $isLocked = ! $progresLevel || $progresLevel->status === ProgresSiswa::STATUS_TERKUNCI;

            $statusBadge = 'anyar';
            if ($isLocked) {
                $statusBadge = 'terkunci';
            } elseif ($jwb && $jwb->skor_tertinggi >= QuizScoringService::PASS_THRESHOLD) {
                $statusBadge = 'selesai';
            } elseif ($jwb && $jwb->jumlah_percobaan > 0) {
                $statusBadge = 'sedang';
            }

            $tipeLabel = match ($soal->tipe_soal) {
                Soal::TIPE_PILIHAN_GANDA => 'Pilihan Ganda',
                Soal::TIPE_SUSUN_KALIMAT => 'Susun Ukara',
                Soal::TIPE_PENCOCOKAN_ARTI => 'Pencocokan Arti',
                Soal::TIPE_PUZZLE_PAKAIAN_ADAT => 'Puzzle Busana Adat',
                Soal::TIPE_MENULIS_AKSARA => 'Tracing Aksara',
                Soal::TIPE_KUIS_SUARA => 'Kuis Wicara Audio',
                default => 'Latihan',
            };

            $soal->status_badge = $statusBadge;
            $soal->skor_tertinggi = $jwb?->skor_tertinggi ?? 0;
            $soal->exp_diberikan = $jwb?->exp_diberikan ?? 0;
            $soal->tipe_label = $tipeLabel;
            $soal->is_locked = $isLocked;
            $soal->url_kuis = $isLocked ? '#' : $kuisCtrl->urlForSoal($soal);

            return $soal;
        });

        // Quick stats
        $allAccessibleSoalIds = Soal::whereIn('level_materi_id', $accessibleLevelIds)->pluck('id');
        $totalSoal = $allAccessibleSoalIds->count();
        $answered = $jawabanMap->whereIn('soal_id', $allAccessibleSoalIds);
        $totalSelesai = $answered->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
        $rataSkor = $answered->count() > 0 ? (int) round($answered->avg('skor_tertinggi')) : 0;
        $totalExpDiperoleh = (int) $answered->sum('exp_diberikan');

        // First available question for "Mulai Latihan Harian Campuran"
        $firstSoal = Soal::whereIn('level_materi_id', $accessibleLevelIds)
            ->whereNotIn('id', $answered->where('skor_tertinggi', '>=', 100)->pluck('soal_id'))
            ->first() ?? Soal::whereIn('level_materi_id', $accessibleLevelIds)->first();
        $firstSoalUrl = $firstSoal ? $kuisCtrl->urlForSoal($firstSoal) : route('kuis.pilihan-ganda');

        return view('latihan-soal', [
            'siswa' => $siswa,
            'levels' => $levels,
            'selectedLevelId' => $selectedLevelId,
            'soalList' => $soalList,
            'totalSoal' => $totalSoal,
            'totalSelesai' => $totalSelesai,
            'rataSkor' => $rataSkor,
            'totalExpDiperoleh' => $totalExpDiperoleh,
            'firstSoalUrl' => $firstSoalUrl,
        ]);
    }
}

