<?php

namespace App\Services\Aksara;

use App\Models\Soal;

/**
 * Jembatan antara converter Latin → Aksara Jawa dan penyimpanan soal tracing.
 *
 * Memanggil `AksaraJawaConverter` langsung secara in-process (tanpa HTTP),
 * dan menerima converter lewat constructor agar mudah di-mock saat testing.
 */
class AksaraJawaConverterService
{
    public function __construct(
        private readonly AksaraJawaConverter $converter,
    ) {}

    /**
     * Preview hasil konversi (tidak menyimpan apa pun).
     *
     * @return array{aksara: string}
     */
    public function preview(
        string $latinText,
        bool $ketikPepetMode = false,
        bool $ignoreSpace = false,
        bool $aksaraSwaraMode = true,
    ): array {
        $converter = $this->converter->withModes($ketikPepetMode, $ignoreSpace, $aksaraSwaraMode);

        return [
            'aksara' => $converter->convert($latinText),
        ];
    }

    /**
     * Bangun payload soal tracing siap simpan (dipakai guru/superadmin).
     *
     * `kunci_jawaban.paths` diisi di sisi klien (template garis tengah dari font
     * via JS) saat preview, lalu dikirim bersama form. Di sini hanya disiapkan
     * teks aksara + latin sebagai fallback bila paths kosong.
     *
     * @return array{soal_latin: string, soal_aksara: string, kunci_jawaban: array<string, mixed>, tipe_soal: string}
     */
    public function buildTracingSoalPayload(
        string $latinText,
        bool $ketikPepetMode = false,
        bool $ignoreSpace = false,
        bool $aksaraSwaraMode = true,
    ): array {
        $result = $this->preview($latinText, $ketikPepetMode, $ignoreSpace, $aksaraSwaraMode);

        return [
            'soal_latin' => $latinText,
            'soal_aksara' => $result['aksara'],
            'kunci_jawaban' => [
                'aksara' => $result['aksara'],
                'latin' => $latinText,
                'paths' => [],
            ],
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
        ];
    }
}
