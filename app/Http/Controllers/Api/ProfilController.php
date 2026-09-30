<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Rules\UniqueCaseInsensitive;
use App\Rules\UniquePhoneNumber;
use App\Rules\UniqueUserEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Update data diri siswa yang sedang login (versi API dari
 * HomeController::dataProfilUpdate) — agar tombol "Simpan Perubahan"
 * pada aplikasi mobile berfungsi.
 */
class ProfilController extends Controller
{
    /**
     * Simpan pembaruan data diri siswa (PUT/POST /api/profil/data).
     */
    public function update(Request $request): JsonResponse
    {
        /** @var Siswa $siswa */
        $siswa = $request->user();

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', new UniqueCaseInsensitive('siswa', 'nama_lengkap', ignoreId: $siswa->id, customMessage: 'Nama lengkap sudah terdaftar.')],
            'nis' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:4', 'max:20'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:16', new UniquePhoneNumber('siswa', $siswa->id)],
            'email' => ['required', 'email', 'max:255', new UniqueUserEmail('siswa', $siswa->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nis.regex' => 'NIS hanya boleh berisi angka.',
            'nis.min' => 'NIS minimal 4 digit.',
            'nis.max' => 'NIS maksimal 20 digit.',
            'no_telpon.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'no_telpon.min' => 'Nomor telepon minimal 9 digit.',
            'no_telpon.max' => 'Nomor telepon maksimal 16 digit.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
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

        return response()->json([
            'message' => 'Data diri berhasil diperbarui.',
            'data' => $siswa->fresh(),
        ]);
    }
}
