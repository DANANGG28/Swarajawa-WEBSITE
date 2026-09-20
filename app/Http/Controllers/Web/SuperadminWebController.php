<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Services\Aksara\AksaraJawaConverterService;
use App\Services\ProgresService;
use App\Services\TtsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                'level' => LevelMateri::count(),
                'soal' => Soal::count(),
            ],
            'levelTerbaru' => LevelMateri::withCount('soal')->orderBy('urutan')->get(),
        ]);
    }

    // ---------------------------------------------------------------- Guru

    public function guru(Request $request): View
    {
        $query = Guru::query()->withCount('soal');

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('nip', 'like', $term));
        }

        return view('superadmin.guru', [
            'judul' => 'Akun Guru',
            'subjudul' => 'Daftarkan dan kelola akun guru terverifikasi',
            'role' => 'superadmin',
            'active' => 'guru',
            'guruList' => $query->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function guruCreate(): View
    {
        return view('superadmin.tambah-guru', [
            'judul' => 'Daftarake Guru Anyar',
            'subjudul' => 'Tambah akun guru anyar supaya bisa ngatur siswa lan bank soal',
            'role' => 'superadmin',
            'active' => 'guru',
        ]);
    }

    public function guruDetail(Guru $guru): View
    {
        return view('superadmin.detail-guru', [
            'judul' => 'Detail Akun Guru',
            'subjudul' => 'Informasi profil, kontak, lan kredensial akun guru',
            'role' => 'superadmin',
            'active' => 'guru',
            'guru' => $guru,
        ]);
    }

    public function guruEdit(Guru $guru): View
    {
        return view('superadmin.edit-guru', [
            'judul' => 'Sunting Akun Guru',
            'subjudul' => 'Owahi data profil, informasi kontak, foto, lan kredensial guru',
            'role' => 'superadmin',
            'active' => 'guru',
            'guru' => $guru,
        ]);
    }

    public function guruStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:guru,nip'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:guru,email'],
            'password' => ['required', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $folder = resource_path('image/guru');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        Guru::create($data);

        return redirect()->route('superadmin.guru')->with('sukses', 'Akun guru kasil didaftarake.');
    }

    public function guruUpdate(Request $request, Guru $guru): RedirectResponse
    {
        $data = $request->validate([
            'nip' => ['sometimes', 'string', 'max:30', 'unique:guru,nip,'.$guru->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:guru,email,'.$guru->id],
            'password' => ['nullable', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $folder = resource_path('image/guru');
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true, true);
            }
            if ($guru->foto && File::exists($folder.'/'.$guru->foto)) {
                File::delete($folder.'/'.$guru->foto);
            }
            $filename = time().'_'.Str::slug($request->nama_lengkap ?? $guru->nama_lengkap).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move($folder, $filename);
            $data['foto'] = $filename;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $guru->update($data);

        return redirect()->route('superadmin.guru')->with('sukses', 'Data akun guru kasil dianyari.');
    }

    public function guruDestroy(Guru $guru): RedirectResponse
    {
        if ($guru->foto && File::exists(resource_path('image/guru/'.$guru->foto))) {
            File::delete(resource_path('image/guru/'.$guru->foto));
        }

        $guru->delete();

        return redirect()->route('superadmin.guru')->with('sukses', 'Akun guru kasil dibusak.');
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
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $siswa = Siswa::create($data);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        return redirect()->route('superadmin.siswa')->with('sukses', 'Akun siswa kasil didaftarake.');
    }

    public function siswaUpdate(Request $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['sometimes', 'string', 'max:30', 'unique:siswa,nis,'.$siswa->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:siswa,email,'.$siswa->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $siswa->update($data);

        return redirect()->route('superadmin.siswa')->with('sukses', 'Akun siswa kasil dianyari.');
    }

    public function siswaDestroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('superadmin.siswa')->with('sukses', 'Akun siswa kasil dibusak.');
    }

    // --------------------------------------------------------- Level Materi

    public function levelMateri(): View
    {
        return view('superadmin.level-materi', [
            'judul' => 'Manajemen Soal & Level',
            'subjudul' => 'Pilih level materi kanggo ngatur bank soal utawa tambah level anyar',
            'role' => 'superadmin',
            'active' => 'level-materi',
            'levels' => LevelMateri::withCount('soal')->orderBy('urutan')->get(),
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

        return back()->with('sukses', 'Level materi kasil digawe.');
    }

    public function levelMateriUpdate(Request $request, LevelMateri $levelMateri): RedirectResponse
    {
        $levelMateri->update($this->validatedLevel($request));

        return back()->with('sukses', 'Level materi kasil dianyari.');
    }

    public function levelMateriDestroy(LevelMateri $levelMateri): RedirectResponse
    {
        $levelMateri->delete();

        return back()->with('sukses', 'Level materi kasil dibusak.');
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
            ->with('levelMateri')
            ->where('level_materi_id', $levelId);

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
            'active' => 'soal',
            'soalList' => $query->latest()->paginate(12)->withQueryString(),
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
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
            'active' => 'soal',
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
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

        return redirect()->route('superadmin.soal', ['level_materi_id' => $data['level_materi_id']])->with('sukses', 'Soal kasil disimpen.');
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

        return back()->with('sukses', 'Soal kasil dianyari.');
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

        return back()->with('sukses', 'Soal kasil dibusak.');
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
    private function validatedLevel(Request $request): array
    {
        return $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'reward_exp' => ['required', 'integer', 'min:0', 'max:100000'],
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
            unset($data['ketik_pepet_mode'], $data['ignore_space'], $data['aksara_swara_mode'], $data['soal_aksara']);

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
}
