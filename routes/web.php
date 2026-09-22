<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\GuruWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\KuisSesiController;
use App\Http\Controllers\Web\SuperadminWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Beranda (role-aware) & Halaman Siswa
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('siswa.dashboard');
Route::get('/dashboard', [HomeController::class, 'index']);

Route::middleware('web.auth:siswa')->group(function () {
    Route::get('/pilih-topik', [HomeController::class, 'topik'])->name('siswa.topik');
    Route::get('/pilih-topik/{topik}', [HomeController::class, 'topikPilih'])->name('siswa.topik.pilih');

    Route::get('/papan-skor', [HomeController::class, 'papanSkor'])->name('siswa.papan-skor');
    Route::get('/asisten-ai', fn () => view('asisten-ai'))->name('siswa.asisten');
    Route::get('/profil', [HomeController::class, 'profil'])->name('siswa.profil');
    Route::get('/profil/data', [HomeController::class, 'dataProfil'])->name('siswa.profil.data');
    Route::put('/profil/data', [HomeController::class, 'dataProfilUpdate'])->name('siswa.profil.update');
});

/*
|--------------------------------------------------------------------------
| Autentikasi Web (login terpadu tanpa pilih peran)
|--------------------------------------------------------------------------
*/
Route::get('/masuk', [AuthWebController::class, 'showMasuk'])->name('masuk');
Route::post('/masuk', [AuthWebController::class, 'masuk']);
Route::get('/daftar', [AuthWebController::class, 'showDaftar'])->name('daftar');
Route::post('/daftar', [AuthWebController::class, 'daftar']);
Route::post('/keluar', [AuthWebController::class, 'keluar'])->name('keluar');

/*
|--------------------------------------------------------------------------
| Sesi Kuis Siswa (Blade) — FR-3, FR-4, FR-8, FR-7, FR-22
|--------------------------------------------------------------------------
*/
Route::middleware('web.auth:siswa')->prefix('kuis')->group(function () {
    Route::get('/mulai/{levelMateri}', [KuisSesiController::class, 'mulaiLevel'])->name('kuis.mulai');
    Route::get('/pilihan-ganda', [KuisSesiController::class, 'pilihanGanda'])->name('kuis.pilihan-ganda');
    Route::get('/susun-ukara', [KuisSesiController::class, 'susunUkara'])->name('kuis.susun-ukara');
    Route::get('/wicara-audio', [KuisSesiController::class, 'wicaraAudio'])->name('kuis.wicara-audio');
    Route::get('/speak-to-text', [KuisSesiController::class, 'speakToText'])->name('kuis.speak-to-text');
    Route::get('/latihan-ngomong', [KuisSesiController::class, 'latihanNgomong'])->name('kuis.latihan-ngomong');
    Route::post('/latihan-ngomong/jawab', [KuisSesiController::class, 'latihanNgomongJawab'])->name('kuis.latihan-ngomong.jawab');
    Route::get('/tracing-aksara', [KuisSesiController::class, 'tracingAksara'])->name('kuis.tracing-aksara');

    Route::post('/jawab', [KuisSesiController::class, 'jawab'])->name('kuis.jawab');
    Route::post('/tts', [KuisSesiController::class, 'tts'])->name('kuis.tts');
    Route::post('/stt', [KuisSesiController::class, 'stt'])->name('kuis.stt');
    Route::post('/sts', [KuisSesiController::class, 'sts'])->name('kuis.sts');
    Route::post('/chat', [KuisSesiController::class, 'chat'])->name('kuis.chat')->middleware('throttle:30,1');
    Route::get('/chat/histori', [KuisSesiController::class, 'chatHistori'])->name('kuis.chat.histori');
    Route::get('/chat/sesi/{chatSession}', [KuisSesiController::class, 'chatSesiShow'])->name('kuis.chat.sesi.show');
    Route::delete('/chat/sesi/{chatSession}', [KuisSesiController::class, 'chatSesiDestroy'])->name('kuis.chat.sesi.destroy');
});

/*
|--------------------------------------------------------------------------
| Area Guru — FR-11, FR-12
|--------------------------------------------------------------------------
*/
Route::middleware('web.auth:guru')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruWebController::class, 'dashboard'])->name('dashboard');

    Route::get('/topik', [GuruWebController::class, 'topik'])->name('topik');
    Route::get('/topik/tambah', [GuruWebController::class, 'topikCreate'])->name('topik.create');
    Route::get('/topik/{topik}/edit', [GuruWebController::class, 'topikEdit'])->name('topik.edit');
    Route::post('/topik', [GuruWebController::class, 'topikStore'])->name('topik.store');
    Route::put('/topik/{topik}', [GuruWebController::class, 'topikUpdate'])->name('topik.update');
    Route::delete('/topik/{topik}', [GuruWebController::class, 'topikDestroy'])->name('topik.destroy');

    Route::get('/level-materi', [GuruWebController::class, 'levelMateri'])->name('level-materi');
    Route::get('/level-materi/tambah', [GuruWebController::class, 'levelMateriCreate'])->name('level-materi.create');
    Route::get('/level-materi/{levelMateri}/edit', [GuruWebController::class, 'levelMateriEdit'])->name('level-materi.edit');
    Route::post('/level-materi', [GuruWebController::class, 'levelMateriStore'])->name('level-materi.store');
    Route::put('/level-materi/{levelMateri}', [GuruWebController::class, 'levelMateriUpdate'])->name('level-materi.update');
    Route::delete('/level-materi/{levelMateri}', [GuruWebController::class, 'levelMateriDestroy'])->name('level-materi.destroy');

    Route::get('/pembahasan', [GuruWebController::class, 'pembahasan'])->name('pembahasan');
    Route::post('/pembahasan', [GuruWebController::class, 'pembahasanStore'])->name('pembahasan.store');
    Route::put('/pembahasan/{pembahasan}', [GuruWebController::class, 'pembahasanUpdate'])->name('pembahasan.update');
    Route::delete('/pembahasan/{pembahasan}', [GuruWebController::class, 'pembahasanDestroy'])->name('pembahasan.destroy');

    Route::get('/soal', [GuruWebController::class, 'soal'])->name('soal');
    Route::get('/soal/tambah', [GuruWebController::class, 'soalCreate'])->name('soal.create');
    Route::get('/soal/{soal}/edit', [GuruWebController::class, 'soalEdit'])->name('soal.edit');
    Route::post('/soal/preview-aksara', [GuruWebController::class, 'previewAksara'])->name('soal.preview');
    Route::post('/soal', [GuruWebController::class, 'soalStore'])->name('soal.store');
    Route::put('/soal/{soal}', [GuruWebController::class, 'soalUpdate'])->name('soal.update');
    Route::delete('/soal/{soal}', [GuruWebController::class, 'soalDestroy'])->name('soal.destroy');

    Route::post('/soal/generate-tts', [GuruWebController::class, 'generateTts'])->name('soal.tts');
});

/*
|--------------------------------------------------------------------------
| Area Superadmin — FR-16 s.d. FR-20
|--------------------------------------------------------------------------
*/
Route::middleware('web.auth:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminWebController::class, 'dashboard'])->name('dashboard');

    Route::get('/guru', [SuperadminWebController::class, 'guru'])->name('guru');
    Route::get('/guru/tambah', [SuperadminWebController::class, 'guruCreate'])->name('guru.create');
    Route::get('/guru/{guru}', [SuperadminWebController::class, 'guruDetail'])->name('guru.show');
    Route::get('/guru/{guru}/edit', [SuperadminWebController::class, 'guruEdit'])->name('guru.edit');
    Route::post('/guru', [SuperadminWebController::class, 'guruStore'])->name('guru.store');
    Route::put('/guru/{guru}', [SuperadminWebController::class, 'guruUpdate'])->name('guru.update');
    Route::delete('/guru/{guru}', [SuperadminWebController::class, 'guruDestroy'])->name('guru.destroy');

    Route::get('/siswa', [SuperadminWebController::class, 'siswa'])->name('siswa');
    Route::get('/siswa/tambah', [SuperadminWebController::class, 'siswaCreate'])->name('siswa.create');
    Route::get('/siswa/{siswa}', [SuperadminWebController::class, 'siswaDetail'])->name('siswa.show');
    Route::get('/siswa/{siswa}/edit', [SuperadminWebController::class, 'siswaEdit'])->name('siswa.edit');
    Route::post('/siswa', [SuperadminWebController::class, 'siswaStore'])->name('siswa.store');
    Route::put('/siswa/{siswa}', [SuperadminWebController::class, 'siswaUpdate'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SuperadminWebController::class, 'siswaDestroy'])->name('siswa.destroy');

    Route::get('/topik', [SuperadminWebController::class, 'topik'])->name('topik');
    Route::get('/topik/tambah', [SuperadminWebController::class, 'topikCreate'])->name('topik.create');
    Route::get('/topik/{topik}/edit', [SuperadminWebController::class, 'topikEdit'])->name('topik.edit');
    Route::post('/topik', [SuperadminWebController::class, 'topikStore'])->name('topik.store');
    Route::put('/topik/{topik}', [SuperadminWebController::class, 'topikUpdate'])->name('topik.update');
    Route::delete('/topik/{topik}', [SuperadminWebController::class, 'topikDestroy'])->name('topik.destroy');

    Route::get('/level-materi', [SuperadminWebController::class, 'levelMateri'])->name('level-materi');
    Route::get('/level-materi/tambah', [SuperadminWebController::class, 'levelMateriCreate'])->name('level-materi.create');
    Route::get('/level-materi/{levelMateri}/edit', [SuperadminWebController::class, 'levelMateriEdit'])->name('level-materi.edit');
    Route::post('/level-materi', [SuperadminWebController::class, 'levelMateriStore'])->name('level-materi.store');
    Route::put('/level-materi/{levelMateri}', [SuperadminWebController::class, 'levelMateriUpdate'])->name('level-materi.update');
    Route::delete('/level-materi/{levelMateri}', [SuperadminWebController::class, 'levelMateriDestroy'])->name('level-materi.destroy');

    Route::get('/pembahasan', [SuperadminWebController::class, 'pembahasan'])->name('pembahasan');
    Route::post('/pembahasan', [SuperadminWebController::class, 'pembahasanStore'])->name('pembahasan.store');
    Route::put('/pembahasan/{pembahasan}', [SuperadminWebController::class, 'pembahasanUpdate'])->name('pembahasan.update');
    Route::delete('/pembahasan/{pembahasan}', [SuperadminWebController::class, 'pembahasanDestroy'])->name('pembahasan.destroy');

    Route::get('/soal', [SuperadminWebController::class, 'soal'])->name('soal');
    Route::get('/soal/tambah', [SuperadminWebController::class, 'soalCreate'])->name('soal.create');
    Route::get('/soal/{soal}/edit', [SuperadminWebController::class, 'soalEdit'])->name('soal.edit');
    Route::post('/soal/preview-aksara', [SuperadminWebController::class, 'previewAksara'])->name('soal.preview');
    Route::post('/soal', [SuperadminWebController::class, 'soalStore'])->name('soal.store');
    Route::put('/soal/{soal}', [SuperadminWebController::class, 'soalUpdate'])->name('soal.update');
    Route::delete('/soal/{soal}', [SuperadminWebController::class, 'soalDestroy'])->name('soal.destroy');

    Route::post('/soal/generate-tts', [SuperadminWebController::class, 'generateTts'])->name('soal.tts');
});

Route::get('/storage/image/guru/{filename}', function (string $filename) {
    $path = storage_path('image/guru/'.$filename);
    if (! file_exists($path)) {
        $path = resource_path('image/guru/'.$filename);
    }
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('guru.image');

Route::get('/resources/image/guru/{filename}', function (string $filename) {
    return redirect()->route('guru.image', $filename);
});

Route::get('/storage/image/superadmin/{filename}', function (string $filename) {
    $path = storage_path('image/superadmin/'.$filename);
    if (! file_exists($path)) {
        $path = resource_path('image/superadmin/'.$filename);
    }
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('superadmin.image');

Route::get('/resources/image/superadmin/{filename}', function (string $filename) {
    return redirect()->route('superadmin.image', $filename);
});

Route::get('/storage/image/siswa/{filename}', function (string $filename) {
    $path = storage_path('image/siswa/'.$filename);
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('siswa.image');
