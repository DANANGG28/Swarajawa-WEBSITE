<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Topik;
use App\Services\Aksara\AksaraJawaConverterService;
use App\Services\TtsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruWebController extends Controller
{
    /**
     * FR-11: Pemantauan progres siswa pada kelas/mapel yang diampu.
     */
    public function dashboard(Request $request): View
    {
        $guru = $this->guru();
        $totalLevel = LevelMateri::count();

        $query = $guru->siswa()
            ->with(['exp', 'strek', 'progres'])
            ->withPivot(['kelas', 'mata_pelajaran']);

        if ($request->filled('kelas')) {
            $query->wherePivot('kelas', $request->query('kelas'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('siswa.nama_lengkap', 'like', $term)->orWhere('siswa.nis', 'like', $term));
        }

        $siswa = $query->paginate(10)->withQueryString();

        $kelasList = $guru->siswa()->distinct()->pluck('guru_siswa.kelas')->filter()->values();

        return view('guru.dashboard', [
            'judul' => 'Pemantauan Siswa',
            'subjudul' => 'Progres belajar siswa pada kelas/mapel yang Anda ampu',
            'role' => 'guru',
            'active' => 'dashboard',
            'siswa' => $siswa,
            'kelasList' => $kelasList,
            'totalLevel' => $totalLevel,
        ]);
    }

    // ------------------------------------------------------------- Topik

    public function topik(Request $request): View
    {
        $query = Topik::withCount('units')->orderBy('urutan');

        $request->validate(['q' => ['nullable', 'string', 'max:255']]);

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama', 'like', $term)->orWhere('deskripsi', 'like', $term));
        }

        return view('guru.topik', [
            'judul' => 'Manajemen Topik',
            'subjudul' => 'Kelola topik pembelajaran; setiap topik dapat memuat beberapa unit materi',
            'role' => 'guru',
            'active' => 'topik',
            'topikList' => $query->paginate(8)->withQueryString(),
        ]);
    }

    public function topikCreate(): View
    {
        $maxUrutan = Topik::max('urutan') ?? 0;

        return view('guru.tambah-topik', [
            'judul' => 'Tambah Topik Baru',
            'subjudul' => 'Buat topik pembelajaran baru',
            'role' => 'guru',
            'active' => 'topik',
            'nextUrutan' => $maxUrutan + 1,
        ]);
    }

    public function topikEdit(Topik $topik): View
    {
        return view('guru.edit-topik', [
            'judul' => 'Sunting Topik',
            'subjudul' => "Topik {$topik->urutan} — {$topik->nama}",
            'role' => 'guru',
            'active' => 'topik',
            'topik' => $topik,
        ]);
    }

    public function topikStore(Request $request): RedirectResponse
    {
        Topik::create($this->validatedTopik($request));

        return redirect()->route('guru.topik')->with('sukses', 'Topik berhasil dibuat.');
    }

    public function topikUpdate(Request $request, Topik $topik): RedirectResponse
    {
        $topik->update($this->validatedTopik($request, $topik));

        return redirect()->route('guru.topik')->with('sukses', 'Topik berhasil diperbarui.');
    }

    public function topikDestroy(Topik $topik): RedirectResponse
    {
        $topik->delete();

        return redirect()->route('guru.topik')->with('sukses', 'Topik berhasil dihapus. Unit terkait tetap tersimpan tanpa topik.');
    }

    // --------------------------------------------------------- Level Materi

    /**
     * FR-12: Kelola level materi / unit pembelajaran.
     */
    public function levelMateri(Request $request): View
    {
        $query = LevelMateri::with(['topik'])->withCount(['soal', 'pembahasan'])->orderBy('urutan');

        $request->validate([
            'topik_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

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

        $levels = $query->paginate(5)->withQueryString();

        return view('guru.level-materi', [
            'judul' => 'Manajemen Level Materi',
            'subjudul' => 'Kelola daftar urutan level pembelajaran, reward EXP, dan bank soal',
            'role' => 'guru',
            'active' => 'topik',
            'levels' => $levels,
            'topikList' => Topik::orderBy('urutan')->get(),
            'filterTopikId' => $request->integer('topik_id') ?: null,
        ]);
    }

    public function levelMateriCreate(Request $request): View
    {
        $maxUrutan = LevelMateri::max('urutan') ?? 0;

        return view('guru.tambah-level-materi', [
            'judul' => 'Tambah Level Materi Baru',
            'subjudul' => 'Daftarkan level pembelajaran baru dengan urutan dan reward EXP',
            'role' => 'guru',
            'active' => 'topik',
            'nextUrutan' => $maxUrutan + 1,
            'topikList' => Topik::orderBy('urutan')->get(),
            'selectedTopikId' => $request->integer('topik_id') ?: null,
        ]);
    }

    public function levelMateriEdit(LevelMateri $levelMateri): View
    {
        return view('guru.edit-level-materi', [
            'judul' => 'Sunting Level Materi',
            'subjudul' => "Level {$levelMateri->urutan} — {$levelMateri->nama_materi}",
            'role' => 'guru',
            'active' => 'topik',
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

        $redirectParams = $level->topik_id ? ['topik_id' => $level->topik_id] : [];

        return redirect()->route('guru.level-materi', $redirectParams)->with('sukses', 'Level materi berhasil dibuat.');
    }

    public function levelMateriUpdate(Request $request, LevelMateri $levelMateri): RedirectResponse
    {
        $levelMateri->update($this->validatedLevel($request, $levelMateri));

        $redirectParams = $levelMateri->topik_id ? ['topik_id' => $levelMateri->topik_id] : [];

        return redirect()->route('guru.level-materi', $redirectParams)->with('sukses', 'Level materi berhasil diperbarui.');
    }

    public function levelMateriDestroy(LevelMateri $levelMateri): RedirectResponse
    {
        $topikId = $levelMateri->topik_id;
        $levelMateri->delete();

        $redirectParams = $topikId ? ['topik_id' => $topikId] : [];

        return redirect()->route('guru.level-materi', $redirectParams)->with('sukses', 'Level materi berhasil dihapus.');
    }

    // --------------------------------------------------------- Pembahasan

    /**
     * Kelola pembahasan (sub-materi) untuk sebuah level.
     */
    public function pembahasan(Request $request): View|RedirectResponse
    {
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('guru.level-materi');
        }

        $level = LevelMateri::findOrFail($request->integer('level_materi_id'));

        $pembahasanList = Pembahasan::query()
            ->where('level_materi_id', $level->id)
            ->withCount('soal')
            ->orderBy('urutan')
            ->get();

        return view('guru.pembahasan', [
            'judul' => 'Kelola Pembahasan',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'topik',
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
            ->route('guru.pembahasan', ['level_materi_id' => $data['level_materi_id']])
            ->with('sukses', 'Pembahasan berhasil ditambahkan.');
    }

    public function pembahasanUpdate(Request $request, Pembahasan $pembahasan): RedirectResponse
    {
        $pembahasan->update($this->validatedPembahasan($request, false, $pembahasan));

        return redirect()
            ->route('guru.pembahasan', ['level_materi_id' => $pembahasan->level_materi_id])
            ->with('sukses', 'Pembahasan berhasil diperbarui.');
    }

    public function pembahasanDestroy(Pembahasan $pembahasan): RedirectResponse
    {
        $levelId = $pembahasan->level_materi_id;
        $pembahasan->delete();

        return redirect()
            ->route('guru.pembahasan', ['level_materi_id' => $levelId])
            ->with('sukses', 'Pembahasan berhasil dihapus. Soal terkait tetap tersimpan tanpa pembahasan.');
    }

    // ------------------------------------------------------------- Soal

    /**
     * FR-12: Daftar soal untuk level tertentu.
     */
    public function soal(Request $request): View|RedirectResponse
    {
        // Redirect ke pilihan level jika tidak ada level_materi_id
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('guru.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        $request->validate([
            'pembahasan_id' => ['nullable', 'integer'],
            'tipe_soal' => ['nullable', 'string', Rule::in(array_keys($this->tipeList()))],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

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

        return view('guru.soal', [
            'judul' => 'Manajemen Soal',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'topik',
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
            return redirect()->route('guru.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        return view('guru.soal-create', [
            'judul' => 'Tambah Soal Anyar',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'topik',
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

        return view('guru.edit-soal', [
            'judul' => 'Sunting Soal',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'topik',
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'pembahasanList' => Pembahasan::where('level_materi_id', $level->id)->orderBy('urutan')->get(),
            'soal' => $soal,
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalStore(Request $request, AksaraJawaConverterService $converter): RedirectResponse
    {
        $guru = $this->guru();
        $data = $this->validatedSoal($request, $converter);
        $data['guru_id'] = $guru->id;
        $data['superadmin_id'] = null;

        if ($request->hasFile('file_gambar')) {
            $data['media_gambar_url'] = $request->file('file_gambar')->store('soal_media', 'public');
        }
        if ($request->hasFile('file_audio')) {
            $data['media_audio_url'] = $request->file('file_audio')->store('soal_media', 'public');
        }

        Soal::create($data);

        return redirect()->route('guru.soal', ['level_materi_id' => $data['level_materi_id']])->with('sukses', 'Soal berhasil disimpan.');
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

        return redirect()->route('guru.soal', ['level_materi_id' => $soal->level_materi_id])->with('sukses', 'Soal berhasil diperbarui.');
    }

    public function soalDestroy(Soal $soal): RedirectResponse
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return back()->with('sukses', 'Soal berhasil dihapus.');
    }

    public function generateTts(Request $request, TtsService $ttsService)
    {
        $request->validate(['text' => ['required', 'string', 'max:1000']]);

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

    private function guru(): Guru
    {
        /** @var Guru $guru */
        $guru = Auth::guard('guru')->user();

        return $guru;
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
            'pertanyaan' => ['required', 'string', 'max:5000'],
            'soal_latin' => ['nullable', 'string', 'max:500'],
            'soal_aksara' => ['nullable', 'string', 'max:1000'],
            'ketik_pepet_mode' => ['nullable', 'boolean'],
            'ignore_space' => ['nullable', 'boolean'],
            'aksara_swara_mode' => ['nullable', 'boolean'],
            'opsi_jawaban_raw' => ['nullable', 'string', 'json', 'max:20000'],
            'kunci_jawaban_raw' => ['required', 'string', 'json', 'max:20000'],
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

            // Paths template dikirim dari klien (dihitung via JS dari font).
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
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];

        if ($withLevel) {
            $rules['level_materi_id'] = ['required', 'integer', 'exists:level_materi,id'];
        }

        return $request->validate($rules);
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
            'deskripsi' => ['nullable', 'string', 'max:2000'],
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
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);
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
