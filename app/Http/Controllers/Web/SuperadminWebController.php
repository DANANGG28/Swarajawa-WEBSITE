<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Models\Topik;
use App\Services\Aksara\AksaraJawaConverterService;
use App\Services\ProgresService;
use App\Services\TtsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminWebController extends Controller
{
    public function __construct(private readonly ProgresService $progres) {}

    /**
     * FR-16: Dashboard ringkasan superadmin.
     */
    public function dashboard(): View
    {
        return view('superadmin.dashboard', [
            'judul' => 'Dashboard Superadmin',
            'subjudul' => 'Ringkasan penggunaan sistem Sinau Jowo',
            'role' => 'superadmin',
            'active' => 'dashboard',
            'stat' => [
                'guru' => Guru::count(),
                'siswa' => Siswa::count(),
                'topik' => Topik::count(),
                'level' => LevelMateri::count(),
                'soal' => Soal::count(),
            ],
            'topikList' => Topik::withCount('units')->orderBy('urutan')->get(),
            'levelTerbaru' => LevelMateri::withCount('soal')->orderBy('urutan')->get(),
        ]);
    }

    // ---------------------------------------------------------------- Guru

    public function guru(Request $request): View
    {
        $guruQuery = Guru::query()->withCount('soal');
        $superadminQuery = Superadmin::query();

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $guruQuery->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('nip', 'like', $term)->orWhere('email', 'like', $term));
            $superadminQuery->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('email', 'like', $term));
        }

        $superadmins = $superadminQuery->latest()->get()->map(function ($sa) {
            $sa->role = 'superadmin';
            $sa->nip = null;
            $sa->jenis_kelamin = null;
            $sa->status_pegawaian = 'Superadmin';

            return $sa;
        });

        $gurus = $guruQuery->latest()->get()->map(function ($g) {
            $g->role = 'guru';

            return $g;
        });

        $combined = $superadmins->concat($gurus)->sortByDesc(fn ($item) => $item->created_at)->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $paginatedItems = $combined->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $combined->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $currentUserId = Auth::guard('superadmin')->id();

        return view('superadmin.guru', [
            'judul' => 'Pengelola',
            'subjudul' => 'Daftarkan dan kelola akun guru serta superadmin terverifikasi',
            'role' => 'superadmin',
            'active' => 'guru',
            'guruList' => $paginator,
            'currentUserId' => $currentUserId,
        ]);
    }

    public function guruCreate(): View
    {
        return view('superadmin.tambah-guru', [
            'judul' => 'Daftarkan Pengelola Baru',
            'subjudul' => 'Tambah akun guru atau superadmin baru untuk mengelola sistem Sinau Jowo',
            'role' => 'superadmin',
            'active' => 'guru',
        ]);
    }

    public function guruDetail(Request $request, string $id): View
    {
        $role = $request->query('role', 'guru');
        if ($role === 'superadmin') {
            $pengelola = Superadmin::findOrFail($id);
            $pengelola->role = 'superadmin';
            $pengelola->nip = null;
            $pengelola->jenis_kelamin = null;
            $pengelola->status_pegawaian = 'Superadmin';
        } else {
            $pengelola = Guru::findOrFail($id);
            $pengelola->role = 'guru';
        }

        return view('superadmin.detail-guru', [
            'judul' => 'Detail Akun '.($role === 'superadmin' ? 'Superadmin' : 'Guru'),
            'subjudul' => 'Informasi profil, kontak, dan kredensial akun '.($role === 'superadmin' ? 'superadmin' : 'guru'),
            'role' => 'superadmin',
            'active' => 'guru',
            'guru' => $pengelola,
            'isSuperadmin' => ($role === 'superadmin'),
        ]);
    }

    public function guruEdit(Request $request, string $id): View
    {
        $role = $request->query('role', 'guru');
        if ($role === 'superadmin') {
            $pengelola = Superadmin::findOrFail($id);
            $pengelola->role = 'superadmin';
            $pengelola->nip = null;
            $pengelola->jenis_kelamin = null;
            $pengelola->status_pegawaian = 'Superadmin';
        } else {
            $pengelola = Guru::findOrFail($id);
            $pengelola->role = 'guru';
        }

        return view('superadmin.edit-guru', [
            'judul' => 'Sunting Akun '.($role === 'superadmin' ? 'Superadmin' : 'Guru'),
            'subjudul' => 'Ubah data profil, kontak, dan kredensial '.($role === 'superadmin' ? 'superadmin' : 'guru'),
            'role' => 'superadmin',
            'active' => 'guru',
            'guru' => $pengelola,
            'isSuperadmin' => ($role === 'superadmin'),
        ]);
    }

    public function guruStore(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'guru');

        if ($role === 'superadmin') {
            $data = $request->validate([
                'nama_lengkap' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:superadmin,email'],
                'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
                'password' => ['required', 'string', 'min:6'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            ], $this->validationMessages());

            if ($request->hasFile('foto')) {
                $folder = storage_path('image/superadmin');
                if (! File::isDirectory($folder)) {
                    File::makeDirectory($folder, 0755, true, true);
                }
                $filename = time().'_'.Str::slug($request->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
                $request->file('foto')->move($folder, $filename);
                $data['foto'] = $filename;
            }

            Superadmin::create($data);

            return redirect()->route('superadmin.guru')->with('sukses', 'Akun Superadmin berhasil didaftarkan.');
        }

        $data = $request->validate([
            'nip' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:25', 'unique:guru,nip'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
            'email' => ['required', 'email', 'max:255', 'unique:guru,email'],
            'password' => ['required', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], $this->validationMessages());

        if ($request->hasFile('foto')) {
            $folder = storage_path('image/guru');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        Guru::create($data);

        return redirect()->route('superadmin.guru')->with('sukses', 'Akun Guru berhasil didaftarkan.');
    }

    public function guruUpdate(Request $request, string $id): RedirectResponse
    {
        $role = $request->input('role', $request->query('role', 'guru'));

        if ($role === 'superadmin') {
            $superadmin = Superadmin::findOrFail($id);
            $data = $request->validate([
                'nama_lengkap' => ['sometimes', 'string', 'max:255'],
                'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
                'email' => ['sometimes', 'email', 'max:255', 'unique:superadmin,email,'.$superadmin->id],
                'password' => ['nullable', 'string', 'min:6'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            ], $this->validationMessages());

            if ($request->hasFile('foto')) {
                $folder = storage_path('image/superadmin');
                if (! File::isDirectory($folder)) {
                    File::makeDirectory($folder, 0755, true, true);
                }
                if ($superadmin->foto && File::exists($folder.'/'.$superadmin->foto)) {
                    File::delete($folder.'/'.$superadmin->foto);
                } elseif ($superadmin->foto && File::exists(resource_path('image/superadmin/'.$superadmin->foto))) {
                    File::delete(resource_path('image/superadmin/'.$superadmin->foto));
                }
                $filename = time().'_'.Str::slug($request->nama_lengkap ?? $superadmin->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
                $request->file('foto')->move($folder, $filename);
                $data['foto'] = $filename;
            }

            if (empty($data['password'])) {
                unset($data['password']);
            }

            $superadmin->update($data);

            return redirect()->route('superadmin.guru')->with('sukses', 'Data akun Superadmin berhasil diperbarui.');
        }

        $guru = Guru::findOrFail($id);
        $data = $request->validate([
            'nip' => ['sometimes', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:25', 'unique:guru,nip,'.$guru->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:guru,email,'.$guru->id],
            'password' => ['nullable', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], $this->validationMessages());

        if ($request->hasFile('foto')) {
            $folder = storage_path('image/guru');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            if ($guru->foto && File::exists($folder.'/'.$guru->foto)) {
                File::delete($folder.'/'.$guru->foto);
            } elseif ($guru->foto && File::exists(resource_path('image/guru/'.$guru->foto))) {
                File::delete(resource_path('image/guru/'.$guru->foto));
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap ?? $guru->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $guru->update($data);

        return redirect()->route('superadmin.guru')->with('sukses', 'Data akun Guru berhasil diperbarui.');
    }

    public function guruDestroy(Request $request, string $id): RedirectResponse
    {
        $role = $request->input('role', $request->query('role', 'guru'));

        if ($role === 'superadmin') {
            $currentAdminId = Auth::guard('superadmin')->id();
            if ((int) $id === (int) $currentAdminId) {
                return redirect()->route('superadmin.guru')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            }

            $superadmin = Superadmin::findOrFail($id);
            if ($superadmin->foto && File::exists(storage_path('image/superadmin/'.$superadmin->foto))) {
                File::delete(storage_path('image/superadmin/'.$superadmin->foto));
            } elseif ($superadmin->foto && File::exists(resource_path('image/superadmin/'.$superadmin->foto))) {
                File::delete(resource_path('image/superadmin/'.$superadmin->foto));
            }

            $superadmin->delete();

            return redirect()->route('superadmin.guru')->with('sukses', 'Akun Superadmin berhasil dihapus.');
        }

        $guru = Guru::findOrFail($id);

        if ($guru->foto && File::exists(storage_path('image/guru/'.$guru->foto))) {
            File::delete(storage_path('image/guru/'.$guru->foto));
        } elseif ($guru->foto && File::exists(resource_path('image/guru/'.$guru->foto))) {
            File::delete(resource_path('image/guru/'.$guru->foto));
        }

        $guru->delete();

        return redirect()->route('superadmin.guru')->with('sukses', 'Akun Guru berhasil dihapus.');
    }

    // --------------------------------------------------------------- Siswa

    public function siswa(Request $request): View
    {
        $query = Siswa::query()->with(['exp', 'strek']);

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->query('kelas'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('nis', 'like', $term));
        }

        return view('superadmin.siswa', [
            'judul' => 'Akun Siswa',
            'subjudul' => 'Kelola seluruh akun siswa terdaftar',
            'role' => 'superadmin',
            'active' => 'siswa',
            'siswaList' => $query->latest()->paginate(10)->withQueryString(),
            'kelasList' => Siswa::query()->distinct()->pluck('kelas')->filter()->values(),
        ]);
    }

    public function siswaCreate(): View
    {
        return view('superadmin.tambah-siswa', [
            'judul' => 'Tambah Akun Siswa',
            'subjudul' => 'Daftarake akun siswa anyar supaya bisa ngakses latihan lan kuis',
            'role' => 'superadmin',
            'active' => 'siswa',
            'kelasList' => Siswa::query()->distinct()->pluck('kelas')->filter()->values(),
        ]);
    }

    public function siswaDetail(Siswa $siswa): View
    {
        $siswa->load(['exp', 'strek', 'progres.levelMateri']);

        return view('superadmin.detail-siswa', [
            'judul' => 'Detail Akun Siswa',
            'subjudul' => 'Informasi profil, statistik capaian, lan administrasi siswa',
            'role' => 'superadmin',
            'active' => 'siswa',
            'siswa' => $siswa,
        ]);
    }

    public function siswaEdit(Siswa $siswa): View
    {
        return view('superadmin.edit-siswa', [
            'judul' => 'Sunting Akun Siswa',
            'subjudul' => 'Owahi data profil, informasi kontak, kelas, lan sandi siswa',
            'role' => 'superadmin',
            'active' => 'siswa',
            'siswa' => $siswa,
            'kelasList' => Siswa::query()->distinct()->pluck('kelas')->filter()->values(),
        ]);
    }

    public function siswaStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:4', 'max:20', 'unique:siswa,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email'],
            'password' => ['required', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], $this->validationMessages());

        if ($request->hasFile('foto')) {
            $folder = storage_path('image/siswa');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        $siswa = Siswa::create($data);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        return redirect()->route('superadmin.siswa')->with('sukses', 'Akun siswa berhasil didaftarkan.');
    }

    public function siswaUpdate(Request $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['sometimes', 'string', 'regex:/^[0-9]+$/', 'min:4', 'max:20', 'unique:siswa,nis,'.$siswa->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:siswa,email,'.$siswa->id],
            'password' => ['nullable', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], $this->validationMessages());

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

        return redirect()->route('superadmin.siswa')->with('sukses', 'Data akun siswa berhasil diperbarui.');
    }

    public function siswaDestroy(Siswa $siswa): RedirectResponse
    {
        if ($siswa->foto && File::exists(storage_path('image/siswa/'.$siswa->foto))) {
            File::delete(storage_path('image/siswa/'.$siswa->foto));
        }

        $siswa->delete();

        return redirect()->route('superadmin.siswa')->with('sukses', 'Akun siswa berhasil dihapus.');
    }

    // ------------------------------------------------------------- Topik

    public function topik(Request $request): View
    {
        $query = Topik::withCount('units')->orderBy('urutan');

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama', 'like', $term)->orWhere('deskripsi', 'like', $term));
        }

        return view('superadmin.topik', [
            'judul' => 'Manajemen Topik',
            'subjudul' => 'Kelola topik (bagian) pembelajaran; saben topik bisa ngemot pirang-pirang unit',
            'role' => 'superadmin',
            'active' => 'topik',
            'topikList' => $query->paginate(8)->withQueryString(),
        ]);
    }

    public function topikCreate(): View
    {
        $maxUrutan = Topik::max('urutan') ?? 0;

        return view('superadmin.tambah-topik', [
            'judul' => 'Tambah Topik Baru',
            'subjudul' => 'Gawe topik (bagian) pembelajaran anyar',
            'role' => 'superadmin',
            'active' => 'topik',
            'nextUrutan' => $maxUrutan + 1,
        ]);
    }

    public function topikEdit(Topik $topik): View
    {
        return view('superadmin.edit-topik', [
            'judul' => 'Sunting Topik',
            'subjudul' => "Topik {$topik->urutan} — {$topik->nama}",
            'role' => 'superadmin',
            'active' => 'topik',
            'topik' => $topik,
        ]);
    }

    public function topikStore(Request $request): RedirectResponse
    {
        Topik::create($this->validatedTopik($request));

        return redirect()->route('superadmin.topik')->with('sukses', 'Topik berhasil dibuat.');
    }

    public function topikUpdate(Request $request, Topik $topik): RedirectResponse
    {
        $topik->update($this->validatedTopik($request, $topik));

        return redirect()->route('superadmin.topik')->with('sukses', 'Topik berhasil diperbarui.');
    }

    public function topikDestroy(Topik $topik): RedirectResponse
    {
        $topik->delete();

        return redirect()->route('superadmin.topik')->with('sukses', 'Topik berhasil dihapus. Unit terkait tetap tersimpan tanpa topik.');
    }

    // --------------------------------------------------------- Level Materi

    public function levelMateri(Request $request): View
    {
        $query = LevelMateri::with(['topik'])->withCount(['soal', 'pembahasan'])->orderBy('urutan');

        if ($request->filled('topik_id')) {
            $query->where('topik_id', $request->integer('topik_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_materi', 'like', $term)
                    ->orWhere('deskripsi', 'like', $term);
            });
        }

        return view('superadmin.level-materi', [
            'judul' => 'Manajemen Level Materi',
            'subjudul' => 'Kelola daftar urutan level pembelajaran, reward EXP, lan bank soal',
            'role' => 'superadmin',
            'active' => 'level-materi',
            'levels' => $query->paginate(5)->withQueryString(),
            'topikList' => Topik::orderBy('urutan')->get(),
            'filterTopikId' => $request->integer('topik_id') ?: null,
        ]);
    }

    public function levelMateriCreate(): View
    {
        $maxUrutan = LevelMateri::max('urutan') ?? 0;

        return view('superadmin.tambah-level-materi', [
            'judul' => 'Tambah Level Materi Baru',
            'subjudul' => 'Daftarake level pembelajaran anyar kanthi urutan lan reward EXP',
            'role' => 'superadmin',
            'active' => 'level-materi',
            'nextUrutan' => $maxUrutan + 1,
            'topikList' => Topik::orderBy('urutan')->get(),
        ]);
    }

    public function levelMateriEdit(LevelMateri $levelMateri): View
    {
        return view('superadmin.edit-level-materi', [
            'judul' => 'Sunting Level Materi',
            'subjudul' => "Level {$levelMateri->urutan} — {$levelMateri->nama_materi}",
            'role' => 'superadmin',
            'active' => 'level-materi',
            'levelMateri' => $levelMateri,
            'topikList' => Topik::orderBy('urutan')->get(),
        ]);
    }

    public function levelMateriStore(Request $request): RedirectResponse
    {
        $level = LevelMateri::create($this->validatedLevel($request));

        $siswaIds = Siswa::pluck('id');
        foreach ($siswaIds as $sId) {
            ProgresSiswa::firstOrCreate(
                ['siswa_id' => $sId, 'level_materi_id' => $level->id],
                ['status' => ProgresSiswa::STATUS_TERKUNCI]
            );
        }

        return redirect()->route('superadmin.level-materi')->with('sukses', 'Level materi berhasil dibuat.');
    }

    public function levelMateriUpdate(Request $request, LevelMateri $levelMateri): RedirectResponse
    {
        $levelMateri->update($this->validatedLevel($request, $levelMateri));

        return redirect()->route('superadmin.level-materi')->with('sukses', 'Level materi berhasil diperbarui.');
    }

    public function levelMateriDestroy(LevelMateri $levelMateri): RedirectResponse
    {
        $levelMateri->delete();

        return redirect()->route('superadmin.level-materi')->with('sukses', 'Level materi berhasil dihapus.');
    }

    // --------------------------------------------------------- Pembahasan

    public function pembahasan(Request $request): View|RedirectResponse
    {
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('superadmin.level-materi');
        }

        $level = LevelMateri::findOrFail($request->integer('level_materi_id'));

        $pembahasanList = Pembahasan::query()
            ->where('level_materi_id', $level->id)
            ->withCount('soal')
            ->orderBy('urutan')
            ->get();

        return view('superadmin.pembahasan', [
            'judul' => 'Kelola Pembahasan',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'superadmin',
            'active' => 'level-materi',
            'level' => $level,
            'pembahasanList' => $pembahasanList,
        ]);
    }

    public function pembahasanStore(Request $request): RedirectResponse
    {
        $data = $this->validatedPembahasan($request, true);
        $data['urutan'] = (Pembahasan::where('level_materi_id', $data['level_materi_id'])->max('urutan') ?? 0) + 1;

        Pembahasan::create($data);

        return redirect()
            ->route('superadmin.pembahasan', ['level_materi_id' => $data['level_materi_id']])
            ->with('sukses', 'Pembahasan berhasil ditambahkan.');
    }

    public function pembahasanUpdate(Request $request, Pembahasan $pembahasan): RedirectResponse
    {
        $pembahasan->update($this->validatedPembahasan($request, false, $pembahasan));

        return redirect()
            ->route('superadmin.pembahasan', ['level_materi_id' => $pembahasan->level_materi_id])
            ->with('sukses', 'Pembahasan berhasil diperbarui.');
    }

    public function pembahasanDestroy(Pembahasan $pembahasan): RedirectResponse
    {
        $levelId = $pembahasan->level_materi_id;
        $pembahasan->delete();

        return redirect()
            ->route('superadmin.pembahasan', ['level_materi_id' => $levelId])
            ->with('sukses', 'Pembahasan berhasil dihapus. Soal terkait tetap tersimpan tanpa pembahasan.');
    }

    // ------------------------------------------------------------- Soal

    public function soal(Request $request): View|RedirectResponse
    {
        // Redirect ke pilihan level jika tidak ada level_materi_id
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('superadmin.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        $query = Soal::query()
            ->with(['levelMateri', 'pembahasan'])
            ->where('level_materi_id', $levelId);

        if ($request->filled('pembahasan_id')) {
            $query->where('pembahasan_id', $request->integer('pembahasan_id'));
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', (string) $request->query('tipe_soal'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where('pertanyaan', 'like', $term);
        }

        return view('superadmin.soal', [
            'judul' => 'Bank Soal',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'superadmin',
            'active' => 'level-materi',
            'soalList' => $query->latest()->paginate(12)->withQueryString(),
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'pembahasanList' => Pembahasan::where('level_materi_id', $levelId)->orderBy('urutan')->get(),
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalCreate(Request $request): View|RedirectResponse
    {
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('superadmin.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        return view('superadmin.soal-create', [
            'judul' => 'Tambah Soal Anyar',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'superadmin',
            'active' => 'level-materi',
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'pembahasanList' => Pembahasan::where('level_materi_id', $levelId)->orderBy('urutan')->get(),
            'selectedPembahasanId' => $request->integer('pembahasan_id') ?: null,
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalEdit(Soal $soal): View
    {
        $this->authorize('update', $soal);

        $soal->load('levelMateri');
        $level = $soal->levelMateri ?? LevelMateri::findOrFail($soal->level_materi_id);

        return view('superadmin.edit-soal', [
            'judul' => 'Sunting Soal',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'superadmin',
            'active' => 'level-materi',
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'pembahasanList' => Pembahasan::where('level_materi_id', $level->id)->orderBy('urutan')->get(),
            'soal' => $soal,
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalStore(Request $request, AksaraJawaConverterService $converter): RedirectResponse
    {
        $data = $this->validatedSoal($request, $converter);
        $data['superadmin_id'] = $this->superadmin()->id;
        $data['guru_id'] = null;

        if ($request->hasFile('file_gambar')) {
            $data['media_gambar_url'] = $request->file('file_gambar')->store('soal_media', 'public');
        }
        if ($request->hasFile('file_audio')) {
            $data['media_audio_url'] = $request->file('file_audio')->store('soal_media', 'public');
        }

        Soal::create($data);

        return redirect()->route('superadmin.soal', ['level_materi_id' => $data['level_materi_id']])->with('sukses', 'Soal berhasil disimpan.');
    }

    public function soalUpdate(Request $request, Soal $soal, AksaraJawaConverterService $converter): RedirectResponse
    {
        $this->authorize('update', $soal);

        $data = $this->validatedSoal($request, $converter);

        if ($request->hasFile('file_gambar')) {
            $data['media_gambar_url'] = $request->file('file_gambar')->store('soal_media', 'public');
        }
        if ($request->hasFile('file_audio')) {
            $data['media_audio_url'] = $request->file('file_audio')->store('soal_media', 'public');
        }

        $soal->update($data);

        return redirect()->route('superadmin.soal', ['level_materi_id' => $soal->level_materi_id])->with('sukses', 'Soal berhasil diperbarui.');
    }

    /**
     * Live preview Latin → Aksara Jawa (tidak menyimpan apa pun).
     */
    public function previewAksara(Request $request, AksaraJawaConverterService $converter): JsonResponse
    {
        $data = $request->validate([
            'soal_latin' => ['nullable', 'string', 'max:500'],
            'ketik_pepet_mode' => ['nullable', 'boolean'],
            'ignore_space' => ['nullable', 'boolean'],
            'aksara_swara_mode' => ['nullable', 'boolean'],
        ]);

        return response()->json($converter->preview(
            (string) ($data['soal_latin'] ?? ''),
            (bool) ($data['ketik_pepet_mode'] ?? false),
            (bool) ($data['ignore_space'] ?? false),
            (bool) ($data['aksara_swara_mode'] ?? true),
        ));
    }

    public function generateTts(Request $request, TtsService $ttsService)
    {
        $request->validate(['text' => 'required|string']);

        $path = $ttsService->generate($request->text);

        if ($path) {
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal generate TTS. Pastikan edge-tts terpasang di server (lihat setup-edge-tts.sh).',
        ], 500);
    }

    public function soalDestroy(Soal $soal): RedirectResponse
    {
        $soal->delete();

        return back()->with('sukses', 'Soal berhasil dihapus.');
    }

    private function superadmin(): Superadmin
    {
        /** @var Superadmin $admin */
        $admin = Auth::guard('superadmin')->user();

        return $admin;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedLevel(Request $request, ?LevelMateri $level = null): array
    {
        return $request->validate([
            'topik_id' => ['nullable', 'integer', 'exists:topik,id'],
            'nama_materi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('level_materi', 'nama_materi')
                    ->where('topik_id', $request->input('topik_id'))
                    ->ignore($level?->id),
            ],
            'deskripsi' => ['nullable', 'string'],
            'reward_exp' => ['required', 'integer', 'min:0', 'max:100000'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedTopik(Request $request, ?Topik $topik = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('topik', 'nama')->ignore($topik?->id)],
            'deskripsi' => ['nullable', 'string'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedSoal(Request $request, AksaraJawaConverterService $converter): array
    {
        $data = $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
            'pembahasan_id' => ['nullable', 'integer', Rule::exists('pembahasan', 'id')->where('level_materi_id', $request->integer('level_materi_id'))],
            'tipe_soal' => ['required', 'in:pilihan_ganda,susun_kalimat,pencocokan_arti,puzzle_pakaian_adat,menulis_aksara,kuis_suara'],
            'pertanyaan' => ['required', 'string'],
            'soal_latin' => ['nullable', 'string', 'max:500'],
            'soal_aksara' => ['nullable', 'string'],
            'ketik_pepet_mode' => ['nullable', 'boolean'],
            'ignore_space' => ['nullable', 'boolean'],
            'aksara_swara_mode' => ['nullable', 'boolean'],
            'opsi_jawaban_raw' => ['nullable', 'string'],
            'kunci_jawaban_raw' => ['required', 'string'],
            'media_audio_url' => ['nullable', 'string', 'max:2048'],
            'bobot_exp' => ['required', 'integer', 'min:0', 'max:1000'],
            'file_gambar' => ['nullable', 'image', 'max:5120'], // max 5MB
            'file_audio' => ['nullable', 'mimetypes:audio/*', 'max:10240'], // max 10MB
        ]);

        $data['opsi_jawaban'] = $this->decodeJson($data['opsi_jawaban_raw'] ?? null);
        $data['kunci_jawaban'] = $this->decodeJson($data['kunci_jawaban_raw']) ?? [];
        unset($data['opsi_jawaban_raw'], $data['kunci_jawaban_raw']);

        $this->applyTracingPayload($data, $converter);

        return $data;
    }

    /**
     * Untuk tipe menulis_aksara, bangun ulang kunci di server dari teks Latin
     * + toggle, supaya hasil konversi konsisten dan tidak bergantung pada klien.
     *
     * @param  array<string, mixed>  $data
     */
    private function applyTracingPayload(array &$data, AksaraJawaConverterService $converter): void
    {
        if (($data['tipe_soal'] ?? null) !== Soal::TIPE_MENULIS_AKSARA) {
            unset($data['ketik_pepet_mode'], $data['ignore_space'], $data['aksara_swara_mode'], $data['soal_aksara'], $data['soal_latin']);

            return;
        }

        $latin = trim((string) ($data['soal_latin'] ?? ''));

        if ($latin !== '') {
            $payload = $converter->buildTracingSoalPayload(
                $latin,
                (bool) ($data['ketik_pepet_mode'] ?? false),
                (bool) ($data['ignore_space'] ?? false),
                (bool) ($data['aksara_swara_mode'] ?? true),
            );

            $data['soal_latin'] = $payload['soal_latin'];
            $data['soal_aksara'] = $payload['soal_aksara'];

            $clientPaths = $data['kunci_jawaban']['paths'] ?? [];

            $data['kunci_jawaban'] = array_merge($payload['kunci_jawaban'], [
                'paths' => is_array($clientPaths) ? $clientPaths : [],
            ]);
        }

        unset($data['ketik_pepet_mode'], $data['ignore_space'], $data['aksara_swara_mode']);
    }

    /**
     * @return array<mixed>|null
     */
    private function decodeJson(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPembahasan(Request $request, bool $withLevel = false, ?Pembahasan $pembahasan = null): array
    {
        $levelId = $withLevel ? $request->integer('level_materi_id') : $pembahasan?->level_materi_id;

        $rules = [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pembahasan', 'nama')
                    ->where('level_materi_id', $levelId)
                    ->ignore($pembahasan?->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ];

        if ($withLevel) {
            $rules['level_materi_id'] = ['required', 'integer', 'exists:level_materi,id'];
        }

        return $request->validate($rules);
    }

    /**
     * @return array<int, string>
     */
    private function tipeList(): array
    {
        return [
            Soal::TIPE_PILIHAN_GANDA => 'Pilihan Ganda',
            Soal::TIPE_SUSUN_KALIMAT => 'Susun Kalimat',
            Soal::TIPE_PENCOCOKAN_ARTI => 'Pencocokan Arti',
            Soal::TIPE_PUZZLE_PAKAIAN_ADAT => 'Puzzle Pakaian Adat',
            Soal::TIPE_MENULIS_AKSARA => 'Menulis Aksara (Tracing)',
            Soal::TIPE_KUIS_SUARA => 'Kuis Suara (TTS/STT)',
        ];
    }

    /**
     * Pesan kustom validasi form Superadmin.
     *
     * @return array<string, string>
     */
    private function validationMessages(): array
    {
        return [
            'no_telpon.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'no_telpon.min' => 'Nomor telepon minimal 9 digit.',
            'no_telpon.max' => 'Nomor telepon maksimal 16 digit.',
            'nip.regex' => 'NIP hanya boleh berisi angka.',
            'nip.min' => 'NIP minimal 8 digit.',
            'nip.max' => 'NIP maksimal 25 digit.',
            'nis.regex' => 'NIS hanya boleh berisi angka.',
            'nis.min' => 'NIS minimal 4 digit.',
            'nis.max' => 'NIS maksimal 20 digit.',
            'urutan.min' => 'Nomor urutan minimal 1.',
        ];
    }
}
