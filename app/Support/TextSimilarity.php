<?php

namespace App\Support;

class TextSimilarity
{
    /**
     * Normalisasi teks: huruf kecil, buang tanda baca, rapikan spasi.
     */
    public static function normalize(?string $text): string
    {
        $text = mb_strtolower((string) $text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    /**
     * Skor kemiripan 0..1 berbasis Levenshtein (di-normalisasi panjang).
     */
    public static function percent(?string $a, ?string $b): float
    {
        $a = self::normalize($a);
        $b = self::normalize($b);

        if ($a === '' && $b === '') {
            return 1.0;
        }

        $max = max(strlen($a), strlen($b));
        if ($max === 0) {
            return 1.0;
        }

        $distance = levenshtein($a, $b);

        return max(0.0, 1.0 - ($distance / $max));
    }

    /**
     * @return array<int, string>
     */
    public static function tokens(?string $text): array
    {
        $normalized = self::normalize($text);

        return $normalized === '' ? [] : explode(' ', $normalized);
    }

    /**
     * Kemiripan Jaccard antar dua himpunan kata.
     *
     * @param  array<int, string>  $a
     * @param  array<int, string>  $b
     */
    public static function jaccard(array $a, array $b): float
    {
        $a = array_values(array_unique($a));
        $b = array_values(array_unique($b));

        if ($a === [] && $b === []) {
            return 1.0;
        }

        $intersection = count(array_intersect($a, $b));
        $union = count(array_unique(array_merge($a, $b)));

        return $union === 0 ? 0.0 : $intersection / $union;
    }
}
