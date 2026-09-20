<?php

namespace Tests\Unit;

use App\Services\Aksara\AksaraJawaConverter;
use App\Services\Aksara\AksaraJawaConverterService;
use Tests\TestCase;

/**
 * Verifikasi konversi Latin → Aksara Jawa (tugas fitur aksara §7).
 *
 * Catatan: keindahan visual bentuk aksara tetap diverifikasi manual dengan
 * font Noto Sans Javanese; test ini menjaga logika transliterasi & mode.
 */
class AksaraJawaConverterTest extends TestCase
{
    private function convert(string $latin, array $modes = []): string
    {
        return (new AksaraJawaConverter(
            $modes['ketikPepetMode'] ?? false,
            $modes['ignoreSpace'] ?? false,
            $modes['aksaraSwaraMode'] ?? true,
        ))->convert($latin);
    }

    public function test_kalimat_pembuka_hana_caraka(): void
    {
        $this->assertSame(
            "\u{A9B2}\u{A9A4} \u{A995}\u{A9AB}\u{A98F}",
            $this->convert('hana caraka')
        );
    }

    public function test_konsonan_bertumpuk_pinter(): void
    {
        // pa+wulu, na+pangkon, ta+taling, layar (-r)
        $this->assertSame(
            "\u{A9A5}\u{A9B6}\u{A9A4}\u{A9C0}\u{A9A0}\u{A9BA}\u{A982}",
            $this->convert('pinter')
        );
    }

    public function test_sandhangan_panyigeg_ng_r_h(): void
    {
        $this->assertSame("\u{A992}\u{A9B8}\u{A9A4}\u{A9B8}\u{A981}", $this->convert('gunung')); // -ng cecak
        $this->assertSame("\u{A9A5}\u{A9B1}\u{A982}", $this->convert('pasar'));                   // -r layar
        $this->assertSame("\u{A9B1}\u{A9AE}\u{A983}", $this->convert('sawah'));                   // -h wignyan
    }

    public function test_toggle_aksara_swara(): void
    {
        $this->assertSame("\u{A984}\u{A98F}\u{A9B8}", $this->convert('aku'));
        $this->assertSame("\u{A9B2}\u{A98F}\u{A9B8}", $this->convert('aku', ['aksaraSwaraMode' => false]));

        $this->assertSame("\u{A98E}\u{A9A9}\u{A983}", $this->convert('omah'));
        $this->assertSame("\u{A9B2}\u{A9BA}\u{A9B4}\u{A9A9}\u{A983}", $this->convert('omah', ['aksaraSwaraMode' => false]));

        $this->assertSame("\u{A98C}\u{A9A4}\u{A98F}\u{A9C0}", $this->convert('enak'));
        $this->assertSame("\u{A9B2}\u{A9BA}\u{A9A4}\u{A98F}\u{A9C0}", $this->convert('enak', ['aksaraSwaraMode' => false]));
    }

    public function test_mode_ketik_pepet(): void
    {
        // Tanpa mode pêpêt: e = taling.
        $this->assertSame("\u{A9B1}\u{A9BA}\u{A992}", $this->convert('sega'));

        // Dengan mode pêpêt: x = pêpêt.
        $this->assertSame(
            "\u{A9B1}\u{A9BC}\u{A992}",
            $this->convert('sxga', ['ketikPepetMode' => true])
        );
    }

    public function test_toggle_abaikan_spasi(): void
    {
        $denganSpasi = $this->convert('aku sekolah');
        $tanpaSpasi = $this->convert('aku sekolah', ['ignoreSpace' => true]);

        $this->assertStringContainsString(' ', $denganSpasi);
        $this->assertStringNotContainsString(' ', $tanpaSpasi);
    }

    public function test_tanda_baca_dan_spasi_dipertahankan(): void
    {
        $hasil = $this->convert('aku sekolah, kowe piye?');

        $this->assertStringContainsString("\u{A9C8}", $hasil); // pada lingsa (koma)
        $this->assertStringContainsString("\u{A9C9}", $hasil); // pada lungsi (tanya)
    }

    public function test_service_build_payload_tracing(): void
    {
        $service = app(AksaraJawaConverterService::class);
        $payload = $service->buildTracingSoalPayload('ha');

        $this->assertSame('ha', $payload['soal_latin']);
        $this->assertSame("\u{A9B2}", $payload['soal_aksara']);
        $this->assertSame('menulis_aksara', $payload['tipe_soal']);
        $this->assertSame("\u{A9B2}", $payload['kunci_jawaban']['aksara']);
        $this->assertSame('ha', $payload['kunci_jawaban']['latin']);
        $this->assertSame([], $payload['kunci_jawaban']['paths']);
    }
}
