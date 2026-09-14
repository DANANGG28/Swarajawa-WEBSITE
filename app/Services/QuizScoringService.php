<?php

namespace App\Services;

use App\Models\Soal;
use App\Support\DollarRecognizer;
use App\Support\TextSimilarity;

/**
 * Validasi & penilaian jawaban per `tipe_soal` — FR-3, FR-4, FR-5, FR-21, FR-22, FR-7/8.
 *
 * Struktur `opsi_jawaban`/`kunci_jawaban` (JSON) bebas ditentukan tim dev
 * sesuai keputusan PRD §6A. Service ini mendukung bentuk-bentuk yang dipakai
 * seeder dan fleksibel terhadap variasi kunci (`jawaban`/`susunan`/`teks`/...).
 */
class QuizScoringService
{
    public const PASS_THRESHOLD = 70;

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    public function score(Soal $soal, mixed $jawaban): array
    {
        return match ($soal->tipe_soal) {
            Soal::TIPE_PILIHAN_GANDA => $this->scorePilihanGanda($soal, $jawaban),
            Soal::TIPE_SUSUN_KALIMAT => $this->scoreSusunKalimat($soal, $jawaban),
            Soal::TIPE_PENCOCOKAN_ARTI => $this->scorePencocokan($soal, $jawaban),
            Soal::TIPE_PUZZLE_PAKAIAN_ADAT => $this->scorePuzzle($soal, $jawaban),
            Soal::TIPE_MENULIS_AKSARA => $this->scoreMenulisAksara($soal, $jawaban),
            Soal::TIPE_KUIS_SUARA => $this->scoreKuisSuara($soal, $jawaban),
            default => ['benar' => false, 'skor' => 0, 'detail' => ['message' => 'Tipe soal tidak dikenal.']],
        };
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scorePilihanGanda(Soal $soal, mixed $jawaban): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $kunciRaw = $kunci['jawaban'] ?? ($kunci[0] ?? null);

        $kunciNorm = $this->resolvePilihan($soal->opsi_jawaban, $kunciRaw);
        $jawabanNorm = $this->resolvePilihan($soal->opsi_jawaban, $jawaban);

        $benar = $kunciNorm !== '' && $kunciNorm === $jawabanNorm;

        return [
            'benar' => $benar,
            'skor' => $benar ? 100 : 0,
            'detail' => ['kunci' => $kunciNorm, 'jawaban' => $jawabanNorm],
        ];
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scoreSusunKalimat(Soal $soal, mixed $jawaban): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $expected = $kunci['susunan'] ?? $kunci['jawaban'] ?? [];

        $expectedArr = $this->toArray($expected);
        $jawabanArr = $this->toArray($jawaban);

        $expectedNorm = array_map([TextSimilarity::class, 'normalize'], $expectedArr);
        $jawabanNorm = array_map([TextSimilarity::class, 'normalize'], $jawabanArr);

        $skor = $this->positionalScore($expectedNorm, $jawabanNorm);
        $benar = $expectedNorm !== [] && $expectedNorm === $jawabanNorm;

        return [
            'benar' => $benar,
            'skor' => $skor,
            'detail' => ['kunci' => $expectedNorm, 'jawaban' => $jawabanNorm],
        ];
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scorePencocokan(Soal $soal, mixed $jawaban): array
    {
        $pasangan = data_get($soal->kunci_jawaban, 'pasangan', []);
        $jawabanMap = is_array($jawaban) ? $jawaban : [];

        if (! is_array($pasangan) || $pasangan === []) {
            return ['benar' => false, 'skor' => 0, 'detail' => ['message' => 'Kunci pasangan kosong.']];
        }

        $correct = 0;
        foreach ($pasangan as $key => $value) {
            if (isset($jawabanMap[$key]) && TextSimilarity::normalize((string) $jawabanMap[$key]) === TextSimilarity::normalize((string) $value)) {
                $correct++;
            }
        }

        $skor = (int) round(($correct / count($pasangan)) * 100);

        return [
            'benar' => $skor === 100,
            'skor' => $skor,
            'detail' => ['benar_berapa' => $correct, 'total' => count($pasangan)],
        ];
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scorePuzzle(Soal $soal, mixed $jawaban): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $expected = $kunci['urutan'] ?? $kunci['jawaban'] ?? [];

        $expectedNorm = array_map('strval', $this->toArray($expected));
        $jawabanNorm = array_map('strval', $this->toArray($jawaban));

        $skor = $this->positionalScore($expectedNorm, $jawabanNorm);
        $benar = $expectedNorm !== [] && $expectedNorm === $jawabanNorm;

        return [
            'benar' => $benar,
            'skor' => $skor,
            'detail' => ['kunci' => $expectedNorm, 'jawaban' => $jawabanNorm],
        ];
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scoreMenulisAksara(Soal $soal, mixed $jawaban): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $template = $kunci['paths'] ?? $kunci['strokes'] ?? [];

        $strokes = is_array($jawaban) && isset($jawaban['strokes'])
            ? $jawaban['strokes']
            : (is_array($jawaban) ? $jawaban : []);

        $similarity = DollarRecognizer::similarity($strokes, is_array($template) ? $template : []);
        $skor = (int) round($similarity * 100);
        $benar = $skor >= self::PASS_THRESHOLD;

        return [
            'benar' => $benar,
            'skor' => $skor,
            'detail' => ['kemiripan' => round($similarity, 3), 'ambang_lulus' => self::PASS_THRESHOLD],
        ];
    }

    /**
     * @return array{benar: bool, skor: int, detail: array<string, mixed>}
     */
    private function scoreKuisSuara(Soal $soal, mixed $jawaban): array
    {
        $kunci = $soal->kunci_jawaban ?? [];
        $expected = (string) ($kunci['teks'] ?? $kunci['jawaban'] ?? '');

        $transcript = is_array($jawaban)
            ? (string) ($jawaban['transcript'] ?? '')
            : (string) $jawaban;

        $similarity = TextSimilarity::percent($transcript, $expected);
        $skor = (int) round($similarity * 100);
        $benar = $skor >= self::PASS_THRESHOLD;

        return [
            'benar' => $benar,
            'skor' => $skor,
            'detail' => ['transcript' => $transcript, 'kunci' => $expected, 'ambang_lulus' => self::PASS_THRESHOLD],
        ];
    }

    private function resolvePilihan(mixed $opsi, mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_array($opsi)) {
            foreach ($opsi as $index => $option) {
                if (is_array($option)) {
                    $label = (string) ($option['label'] ?? $option['id'] ?? $index);
                    $teks = (string) ($option['teks'] ?? $option['text'] ?? $option['value'] ?? '');

                    if (TextSimilarity::normalize($label) === TextSimilarity::normalize((string) $value)) {
                        return TextSimilarity::normalize($teks);
                    }
                    if (TextSimilarity::normalize($teks) === TextSimilarity::normalize((string) $value)) {
                        return TextSimilarity::normalize($teks);
                    }
                } else {
                    $teks = (string) $option;
                    if ((string) $index === (string) $value) {
                        return TextSimilarity::normalize($teks);
                    }
                    if (TextSimilarity::normalize($teks) === TextSimilarity::normalize((string) $value)) {
                        return TextSimilarity::normalize($teks);
                    }
                }
            }
        }

        return TextSimilarity::normalize((string) $value);
    }

    /**
     * @return array<int, mixed>
     */
    private function toArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if ($value === null || $value === '') {
            return [];
        }

        return preg_split('/\s+/', trim((string) $value)) ?: [];
    }

    /**
     * Skor posisi: persentase elemen yang cocok pada indeks yang sama.
     *
     * @param  array<int, string>  $expected
     * @param  array<int, string>  $actual
     */
    private function positionalScore(array $expected, array $actual): int
    {
        $total = count($expected);
        if ($total === 0) {
            return 0;
        }

        $match = 0;
        for ($i = 0; $i < $total; $i++) {
            if (($actual[$i] ?? null) === $expected[$i]) {
                $match++;
            }
        }

        return (int) round(($match / $total) * 100);
    }
}
