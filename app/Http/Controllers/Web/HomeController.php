<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exp;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Topik;
use App\Services\GamificationService;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use App\Support\AuthContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

            // Unit (level_materi) & progres
            $allLevels = LevelMateri::query()
                ->withCount(['soal', 'pembahasan'])
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
                        'topik_id' => $level->topik_id,
                        'urutan' => $level->urutan,
                        'nama_materi' => $level->nama_materi,
                        'deskripsi' => $level->deskripsi,
                        'reward_exp' => $level->reward_exp,
                        'total_soal' => $totalSoal,
                        'pembahasan_count' => $level->pembahasan_count,
                        'lulus_count' => $lulusCount,
                        'persen' => $persen,
                        'rata_skor' => $rataSkor,
                        'status' => $status,
                    ];
                });

            // Topik (kategori paling dhuwur): 1 topik -> akeh unit.
            $topikList = Topik::query()->orderBy('urutan')->get();

            $topikAktif = null;
            $topikId = session('topik_id');
            if ($topikId) {
                $topikAktif = $topikList->firstWhere('id', (int) $topikId);
            }
            if (! $topikAktif) {
                $unitBerjalan = $allLevels->firstWhere('status', ProgresSiswa::STATUS_BERJALAN);
                if ($unitBerjalan && $unitBerjalan->topik_id) {
                    $topikAktif = $topikList->firstWhere('id', $unitBerjalan->topik_id);
                }
            }
            if (! $topikAktif) {
                $topikAktif = $topikList->first();
            }

            // Unit sing ditampilake mung unit saka topik sing dipilih.
            $levels = $topikAktif
                ? $allLevels->where('topik_id', $topikAktif->id)->values()
                : $allLevels->values();

            $activeLevel = $levels->firstWhere('status', ProgresSiswa::STATUS_BERJALAN)
                ?? $levels->firstWhere('status', ProgresSiswa::STATUS_SELESAI)
                ?? $levels->first();

            $accessibleLevelIds = $allLevels->where('status', '!=', ProgresSiswa::STATUS_TERKUNCI)->pluck('id');

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

            // Pembahasan (sub-materi) saben level -> dadi node ing roadmap per unit.
            $pembahasanByLevel = [];
            foreach ($levels as $lvl) {
                $levelTerkunci = $lvl->status === ProgresSiswa::STATUS_TERKUNCI;

                $pembahasanByLevel[$lvl->id] = Pembahasan::query()
                    ->where('level_materi_id', $lvl->id)
                    ->withCount('soal')
                    ->orderBy('urutan')
                    ->get()
                    ->map(function (Pembahasan $pembahasan) use ($siswa, $levelTerkunci) {
                        $soalIds = $pembahasan->soal()->pluck('id');
                        $jawaban = JawabanSiswa::where('siswa_id', $siswa->id)
                            ->whereIn('soal_id', $soalIds)
                            ->get();

                        $lulus = $jawaban->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
                        $total = $pembahasan->soal_count;
                        $persen = $total > 0 ? (int) round(($lulus / $total) * 100) : 0;

                        $status = ($total > 0 && $lulus >= $total)
                            ? 'selesai'
                            : ($jawaban->count() > 0 ? 'berjalan' : 'anyar');

                        return (object) [
                            'id' => $pembahasan->id,
                            'nama' => $pembahasan->nama,
                            'deskripsi' => $pembahasan->deskripsi,
                            'urutan' => $pembahasan->urutan,
                            'total_soal' => $total,
                            'lulus_count' => $lulus,
                            'persen' => $persen,
                            'status' => $status,
                            'terkunci' => $levelTerkunci,
                            'mulai_url' => route('kuis.mulai', [
                                'levelMateri' => $pembahasan->level_materi_id,
                                'pembahasan_id' => $pembahasan->id,
                            ]),
                        ];
                    });
            }

            $activeLevelPembahasan = $activeLevel
                ? ($pembahasanByLevel[$activeLevel->id] ?? collect())
                : collect();

            $fokusPembahasan = $activeLevelPembahasan->firstWhere('status', '!=', 'selesai')
                ?? $activeLevelPembahasan->first();

            // Misi harian (dihitung dari jawaban hari ini).
            $jawabanHariIni = JawabanSiswa::with('soal')
                ->where('siswa_id', $siswa->id)
                ->whereDate('updated_at', now()->toDateString())
                ->get();

            $lulusHariIni = $jawabanHariIni->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)->count();
            $expHariIni = (int) $jawabanHariIni->sum('exp_diberikan');
            $suaraHariIni = $jawabanHariIni->filter(fn ($j) => $j->soal?->tipe_soal === Soal::TIPE_KUIS_SUARA)->count();

            $misiHarian = [
                ['label' => 'Selesaikan 2 Soal', 'progress' => min($lulusHariIni, 2), 'target' => 2, 'warna' => 'emerald'],
                ['label' => 'Dapatkan 50 XP Hari Ini', 'progress' => min($expHariIni, 50), 'target' => 50, 'warna' => 'brand'],
                ['label' => 'Latihan Bicara AI (STT) 1×', 'progress' => min($suaraHariIni, 1), 'target' => 1, 'warna' => 'brand-300'],
            ];

            // Kalender streak 7 hari (Senin s.d. Minggu minggu ini).
            $namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $awalMinggu = now()->startOfWeek();
            $tanggalAktif = JawabanSiswa::where('siswa_id', $siswa->id)
                ->where('updated_at', '>=', $awalMinggu)
                ->get(['updated_at'])
                ->map(fn ($j) => $j->updated_at->toDateString())
                ->unique();

            $weekStreak = collect(range(0, 6))->map(function (int $i) use ($awalMinggu, $tanggalAktif, $namaHari) {
                $tanggal = $awalMinggu->copy()->addDays($i);

                return [
                    'label' => $namaHari[$tanggal->dayOfWeek],
                    'aktif' => $tanggalAktif->contains($tanggal->toDateString()),
                    'is_today' => $tanggal->isToday(),
                    'is_future' => $tanggal->isFuture(),
                ];
            });

            return view('welcome', [
                'siswa' => $siswa,
                'totalExp' => $totalExp,
                'currentStreak' => $currentStreak,
                'highestStreak' => $highestStreak,
                'levels' => $levels,
                'topikList' => $topikList,
                'topikAktif' => $topikAktif,
                'activeLevel' => $activeLevel,
                'pembahasanByLevel' => $pembahasanByLevel,
                'fokusPembahasan' => $fokusPembahasan,
                'misiHarian' => $misiHarian,
                'weekStreak' => $weekStreak,
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
     * Kaca pilih topik: dhaptar topik (bagian) kang bisa dipilih siswa.
     */
    public function topik(Request $request): View
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);
        $this->gamification->syncStreak($siswa);

        if ($siswa->progres()->count() === 0) {
            $this->progres->initialize($siswa);
        }

        $topikList = Topik::query()->with('units')->orderBy('urutan')->get();

        $lulusIds = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id');

        $topikCards = $topikList->map(function (Topik $topik) use ($siswa, $lulusIds) {
            $unitIds = $topik->units->pluck('id');
            $soalIds = Soal::whereIn('level_materi_id', $unitIds)->pluck('id');
            $totalSoal = $soalIds->count();
            $lulus = $lulusIds->intersect($soalIds)->count();
            $persen = $totalSoal > 0 ? (int) round(($lulus / $totalSoal) * 100) : 0;

            $unitSelesai = ProgresSiswa::where('siswa_id', $siswa->id)
                ->whereIn('level_materi_id', $unitIds)
                ->where('status', ProgresSiswa::STATUS_SELESAI)
                ->count();

            $status = ($totalSoal > 0 && $lulus >= $totalSoal)
                ? 'selesai'
                : ($lulus > 0 ? 'berjalan' : 'anyar');

            return (object) [
                'id' => $topik->id,
                'nama' => $topik->nama,
                'deskripsi' => $topik->deskripsi,
                'urutan' => $topik->urutan,
                'total_unit' => $unitIds->count(),
                'unit_selesai' => $unitSelesai,
                'total_soal' => $totalSoal,
                'lulus_count' => $lulus,
                'persen' => $persen,
                'status' => $status,
            ];
        });

        $defaultId = 0;
        $berjalan = $topikCards->firstWhere('status', 'berjalan');
        if ($berjalan) {
            $defaultId = $berjalan->id;
        } elseif ($topikCards->isNotEmpty()) {
            $defaultId = $topikCards->first()->id;
        }

        $topikAktifId = (int) ($request->session()->get('topik_id') ?? $defaultId);

        return view('pilih-topik', [
            'siswa' => $siswa,
            'topikCards' => $topikCards,
            'topikAktifId' => $topikAktifId,
        ]);
    }

    /**
     * Pilih topik: simpen ing session banjur bali menyang beranda.
     */
    public function topikPilih(Request $request, Topik $topik): RedirectResponse
    {
        $request->session()->put('topik_id', $topik->id);

        return redirect()->route('siswa.dashboard');
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

    /**
     * Halaman melengkapi & edit data diri siswa.
     */
    public function dataProfil(Request $request): View
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);

        $fields = [
            'foto' => ! empty($siswa->foto),
            'nama_lengkap' => ! empty($siswa->nama_lengkap),
            'nis' => ! empty($siswa->nis),
            'jenis_kelamin' => ! empty($siswa->jenis_kelamin),
            'kelas' => ! empty($siswa->kelas),
            'no_telpon' => ! empty($siswa->no_telpon),
            'email' => ! empty($siswa->email),
        ];

        $totalFields = count($fields);
        $completedFields = count(array_filter($fields));
        $persenLengkap = (int) round(($completedFields / $totalFields) * 100);

        $fieldLabels = [
            'foto' => 'Foto Profil',
            'nama_lengkap' => 'Nama Lengkap',
            'nis' => 'Nomor Induk Siswa (NIS)',
            'jenis_kelamin' => 'Jenis Kelamin',
            'kelas' => 'Kelas',
            'no_telpon' => 'Nomor Telepon',
            'email' => 'Email',
        ];

        $belumLengkap = [];
        foreach ($fields as $key => $isFilled) {
            if (! $isFilled) {
                $belumLengkap[] = $fieldLabels[$key];
            }
        }

        return view('data-profil-siswa', [
            'siswa' => $siswa,
            'persenLengkap' => $persenLengkap,
            'completedFields' => $completedFields,
            'totalFields' => $totalFields,
            'belumLengkap' => $belumLengkap,
        ]);
    }

    /**
     * Simpan pembaruan data diri siswa.
     */
    public function dataProfilUpdate(Request $request): RedirectResponse
    {
        /** @var Siswa $siswa */
        $siswa = AuthContext::currentUser($request);

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'string', 'max:30', 'unique:siswa,nis,'.$siswa->id],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email,'.$siswa->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, WEBP, atau SVG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('foto')) {
            $folder = storage_path('image/siswa');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            if ($siswa->foto && File::exists($folder.'/'.$siswa->foto)) {
                File::delete($folder.'/'.$siswa->foto);
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap ?? $siswa->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $siswa->update($data);

        return redirect()->route('siswa.profil')->with('sukses', 'Data diri Anda berhasil diperbarui!');
    }
}
