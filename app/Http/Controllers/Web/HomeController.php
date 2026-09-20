<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exp;
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
                ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
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

        return view('landing');
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

        $levelQuery = LevelMateri::query()->withCount('soal')->orderBy('urutan');
        if ($request->filled('q')) {
            $levelQuery->where('nama_materi', 'like', '%'.$request->q.'%');
        }
        $levels = $levelQuery->get();

        $progresMap = ProgresSiswa::where('siswa_id', $siswa->id)->get()->keyBy('level_materi_id');
        $jawabanMap = JawabanSiswa::where('siswa_id', $siswa->id)->get()->keyBy('soal_id');

        $tipeLabels = [
            Soal::TIPE_PILIHAN_GANDA => 'Pilihan Ganda',
            Soal::TIPE_SUSUN_KALIMAT => 'Susun Ukara',
            Soal::TIPE_PENCOCOKAN_ARTI => 'Pencocokan Arti',
            Soal::TIPE_PUZZLE_PAKAIAN_ADAT => 'Puzzle Busana Adat',
            Soal::TIPE_MENULIS_AKSARA => 'Tracing Aksara',
            Soal::TIPE_KUIS_SUARA => 'Kuis Wicara Audio',
        ];

        // Siji card saben level materi.
        $levelCards = $levels->map(function (LevelMateri $level) use ($progresMap, $jawabanMap, $tipeLabels) {
            $progres = $progresMap->get($level->id);
            $status = $progres?->status ?? ProgresSiswa::STATUS_TERKUNCI;
            $isLocked = $status === ProgresSiswa::STATUS_TERKUNCI;

            $soalLevel = Soal::where('level_materi_id', $level->id)->get(['id', 'tipe_soal', 'bobot_exp']);
            $soalLevelIds = $soalLevel->pluck('id');
            $total = $soalLevel->count();
            $jawabanLevel = $jawabanMap->filter(fn ($j) => $soalLevelIds->contains($j->soal_id));
            $lulus = $jawabanLevel->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
            $persen = $total > 0 ? (int) round(($lulus / $total) * 100) : 0;
            $rataSkor = $jawabanLevel->count() > 0 ? (int) round($jawabanLevel->avg('skor_tertinggi')) : 0;

            if ($isLocked) {
                $badge = 'terkunci';
            } elseif ($total > 0 && $lulus >= $total) {
                $badge = 'selesai';
            } elseif ($jawabanLevel->count() > 0) {
                $badge = 'sedang';
            } else {
                $badge = 'anyar';
            }

            $tipeList = $soalLevel->pluck('tipe_soal')->unique()
                ->map(fn ($t) => $tipeLabels[$t] ?? 'Latihan')->values();

            return (object) [
                'id' => $level->id,
                'urutan' => $level->urutan,
                'nama_materi' => $level->nama_materi,
                'deskripsi' => $level->deskripsi,
                'reward_exp' => (int) $level->reward_exp,
                'total_soal' => $total,
                'lulus_count' => $lulus,
                'persen' => $persen,
                'rata_skor' => $rataSkor,
                'status' => $status,
                'badge' => $badge,
                'is_locked' => $isLocked,
                'tipe_list' => $tipeList,
                'mulai_url' => $isLocked ? null : route('kuis.mulai', $level),
            ];
        });

        // Quick stats across accessible levels.
        $accessibleLevelIds = $progresMap->where('status', '!=', ProgresSiswa::STATUS_TERKUNCI)->pluck('level_materi_id');
        $accessibleSoalIds = Soal::whereIn('level_materi_id', $accessibleLevelIds)->pluck('id');
        $answered = $jawabanMap->filter(fn ($j) => $accessibleSoalIds->contains($j->soal_id));
        $totalSoal = $accessibleSoalIds->count();
        $totalSelesai = $answered->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
        $rataSkor = $answered->count() > 0 ? (int) round($answered->avg('skor_tertinggi')) : 0;
        $totalExpDiperoleh = (int) $answered->sum('exp_diberikan');

        // Soal pertama kanggo tombol "Mulai Latihan Harian Campuran".
        $kuisCtrl = app(KuisSesiController::class);
        $firstSoal = Soal::whereIn('level_materi_id', $accessibleLevelIds)
            ->whereNotIn('id', $answered->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->pluck('soal_id'))
            ->first() ?? Soal::whereIn('level_materi_id', $accessibleLevelIds)->first();
        $firstSoalUrl = $firstSoal ? $kuisCtrl->urlForSoal($firstSoal) : route('kuis.pilihan-ganda');

        return view('latihan-soal', [
            'siswa' => $siswa,
            'levelCards' => $levelCards,
            'totalSoal' => $totalSoal,
            'totalSelesai' => $totalSelesai,
            'rataSkor' => $rataSkor,
            'totalExpDiperoleh' => $totalExpDiperoleh,
            'firstSoalUrl' => $firstSoalUrl,
        ]);
    }

    /**
     * Halaman papan skor / leaderboard dinamis.
     */
    public function papanSkor(Request $request): View
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);
        $this->gamification->syncStreak($siswa);

        $kelasSiswa = $siswa->kelas;
        $scope = $request->query('scope') === 'sekolah' ? 'sekolah' : 'kelas';
        $filterKelas = $scope === 'sekolah' ? null : $kelasSiswa;

        $leaderboard = $this->gamification->leaderboard($filterKelas, 50);
        $juaraSekolah = $this->gamification->leaderboard(null, 1)[0] ?? null;

        $myExp = (int) ($siswa->exp()->value('total_exp') ?? 0);
        $myStreak = (int) ($siswa->strek()->value('current_streak') ?? 0);

        $expQuery = Exp::query();
        if ($filterKelas) {
            $expQuery->whereHas('siswa', fn ($q) => $q->where('kelas', $filterKelas));
        }
        $myRank = $expQuery->where('total_exp', '>', $myExp)->count() + 1;

        $top1 = $leaderboard[0] ?? null;
        $top2 = $leaderboard[1] ?? null;
        $top3 = $leaderboard[2] ?? null;
        $others = array_slice($leaderboard, 3);

        return view('papan-skor', [
            'siswa' => $siswa,
            'myExp' => $myExp,
            'myStreak' => $myStreak,
            'myRank' => $myRank,
            'top1' => $top1,
            'top2' => $top2,
            'top3' => $top3,
            'others' => $others,
            'juaraSekolah' => $juaraSekolah,
            'kelasSiswa' => $kelasSiswa,
            'scope' => $scope,
        ]);
    }

    /**
     * Halaman profil siswa dinamis.
     */
    public function profil(Request $request): View
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);
        $this->gamification->syncStreak($siswa);

        $totalExp = (int) ($siswa->exp()->value('total_exp') ?? 0);
        $strek = $siswa->strek()->first();
        $currentStreak = (int) ($strek?->current_streak ?? 0);
        $highestStreak = (int) ($strek?->highest_streak ?? 0);

        $myRank = Exp::where('total_exp', '>', $totalExp)->count() + 1;

        $completedLevels = ProgresSiswa::where('siswa_id', $siswa->id)
            ->where('status', ProgresSiswa::STATUS_SELESAI)
            ->count();
        $totalLevels = LevelMateri::count();

        $completedQuestions = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->count();
        $totalQuestions = Soal::count();

        $guruPangampu = $siswa->guru()->get();

        return view('profil', [
            'siswa' => $siswa,
            'totalExp' => $totalExp,
            'currentStreak' => $currentStreak,
            'highestStreak' => $highestStreak,
            'myRank' => $myRank,
            'completedLevels' => $completedLevels,
            'totalLevels' => $totalLevels,
            'completedQuestions' => $completedQuestions,
            'totalQuestions' => $totalQuestions,
            'guruPangampu' => $guruPangampu,
        ]);
    }
}
