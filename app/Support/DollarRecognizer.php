<?php

namespace App\Support;

/**
 * Implementasi ringan algoritma $1 Unistroke Recognizer (PRD §6D, FR-22).
 *
 * Dipakai untuk menghitung skor kemiripan goresan siswa terhadap template
 * aksara referensi. Bersifat murni geometris (tanpa ML/dataset training) dan
 * menjadi padanan server-side dari implementasi JS di sisi klien.
 */
class DollarRecognizer
{
    public const NUM_POINTS = 64;

    public const SQUARE_SIZE = 250.0;

    public const ANGLE_RANGE = 45.0;

    public const ANGLE_PRECISION = 2.0;

    public static function halfDiagonal(): float
    {
        return 0.5 * sqrt(2.0 * self::SQUARE_SIZE * self::SQUARE_SIZE);
    }

    /**
     * Bandingkan goresan siswa dengan template, hasilkan skor 0..1.
     *
     * @param  array<int, array<int, array{0: float|int, 1: float|int}>>  $candidateStrokes
     * @param  array<int, array<int, array{0: float|int, 1: float|int}>>  $templateStrokes
     */
    public static function similarity(array $candidateStrokes, array $templateStrokes): float
    {
        $candidate = self::prepare($candidateStrokes);
        $template = self::prepare($templateStrokes);

        if (count($candidate) < 2 || count($template) < 2) {
            return 0.0;
        }

        $distance = self::greedyCloudMatch($candidate, $template);

        return max(0.0, min(1.0, 1.0 - ($distance / self::halfDiagonal())));
    }

    /**
     * @param  array<int, array<int, array{0: float|int, 1: float|int}>>  $strokes
     * @return array<int, array{0: float, 1: float}>
     */
    public static function prepare(array $strokes): array
    {
        $points = [];

        foreach ($strokes as $stroke) {
            foreach ($stroke as $point) {
                if (is_array($point) && count($point) >= 2) {
                    $points[] = [(float) $point[0], (float) $point[1]];
                }
            }
        }

        if (count($points) < 2) {
            return $points;
        }

        $points = self::resample($points, self::NUM_POINTS);
        $points = self::rotateToZero($points, self::indicativeAngle($points));
        $points = self::scaleToSquare($points, self::SQUARE_SIZE);
        $points = self::translateToOrigin($points);

        return $points;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, array{0: float, 1: float}>
     */
    public static function resample(array $points, int $n): array
    {
        $interval = self::pathLength($points) / ($n - 1);
        $distance = 0.0;
        $resampled = [$points[0]];

        $count = count($points);
        for ($i = 1; $i < $count; $i++) {
            $d = self::distance($points[$i - 1], $points[$i]);

            if (($distance + $d) >= $interval && $d > 0.0) {
                $qx = $points[$i - 1][0] + (($interval - $distance) / $d) * ($points[$i][0] - $points[$i - 1][0]);
                $qy = $points[$i - 1][1] + (($interval - $distance) / $d) * ($points[$i][1] - $points[$i - 1][1]);

                $resampled[] = [$qx, $qy];
                $points = array_merge(
                    array_slice($points, 0, $i),
                    [[$qx, $qy]],
                    array_slice($points, $i)
                );
                $distance = 0.0;
                $count = count($points);
            } else {
                $distance += $d;
            }
        }

        while (count($resampled) < $n) {
            $resampled[] = $points[count($points) - 1];
        }

        return array_slice($resampled, 0, $n);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     */
    public static function pathLength(array $points): float
    {
        $length = 0.0;
        for ($i = 1, $n = count($points); $i < $n; $i++) {
            $length += self::distance($points[$i - 1], $points[$i]);
        }

        return $length;
    }

    /**
     * @param  array{0: float, 1: float}  $a
     * @param  array{0: float, 1: float}  $b
     */
    public static function distance(array $a, array $b): float
    {
        return sqrt((($a[0] - $b[0]) ** 2) + (($a[1] - $b[1]) ** 2));
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array{0: float, 1: float}
     */
    public static function centroid(array $points): array
    {
        $x = 0.0;
        $y = 0.0;
        foreach ($points as $point) {
            $x += $point[0];
            $y += $point[1];
        }
        $n = max(1, count($points));

        return [$x / $n, $y / $n];
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     */
    public static function indicativeAngle(array $points): float
    {
        $centroid = self::centroid($points);

        return atan2($centroid[1] - $points[0][1], $centroid[0] - $points[0][0]);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, array{0: float, 1: float}>
     */
    public static function rotateBy(array $points, float $theta): array
    {
        $centroid = self::centroid($points);
        $cos = cos($theta);
        $sin = sin($theta);

        return array_map(function (array $point) use ($centroid, $cos, $sin): array {
            $dx = $point[0] - $centroid[0];
            $dy = $point[1] - $centroid[1];

            return [
                $dx * $cos - $dy * $sin + $centroid[0],
                $dx * $sin + $dy * $cos + $centroid[1],
            ];
        }, $points);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, array{0: float, 1: float}>
     */
    public static function rotateToZero(array $points, float $theta): array
    {
        return self::rotateBy($points, -$theta);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, array{0: float, 1: float}>
     */
    public static function scaleToSquare(array $points, float $size): array
    {
        $minX = $maxX = $points[0][0];
        $minY = $maxY = $points[0][1];

        foreach ($points as $point) {
            $minX = min($minX, $point[0]);
            $maxX = max($maxX, $point[0]);
            $minY = min($minY, $point[1]);
            $maxY = max($maxY, $point[1]);
        }

        $width = max(0.0001, $maxX - $minX);
        $height = max(0.0001, $maxY - $minY);

        return array_map(fn (array $point): array => [
            ($point[0] - $minX) * ($size / $width),
            ($point[1] - $minY) * ($size / $height),
        ], $points);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, array{0: float, 1: float}>
     */
    public static function translateToOrigin(array $points): array
    {
        $centroid = self::centroid($points);

        return array_map(fn (array $point): array => [
            $point[0] - $centroid[0],
            $point[1] - $centroid[1],
        ], $points);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $a
     * @param  array<int, array{0: float, 1: float}>  $b
     */
    public static function pathDistance(array $a, array $b): float
    {
        $d = 0.0;
        $n = min(count($a), count($b));

        for ($i = 0; $i < $n; $i++) {
            $d += self::distance($a[$i], $b[$i]);
        }

        return $d / max(1, $n);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @param  array<int, array{0: float, 1: float}>  $template
     */
    public static function distanceAtBestAngle(array $points, array $template): float
    {
        $start = -self::ANGLE_RANGE;
        $end = self::ANGLE_RANGE;
        $delta = self::ANGLE_PRECISION;

        $phi = 0.5 * (sqrt(5.0) - 1.0);

        $a = $start + (1.0 - $phi) * ($end - $start);
        $b = $start + $phi * ($end - $start);

        $x1 = self::distanceAtAngle($points, $template, $a);
        $x2 = self::distanceAtAngle($points, $template, $b);

        while (abs($end - $start) > $delta) {
            if ($x1 < $x2) {
                $end = $b;
                $b = $a;
                $x2 = $x1;
                $a = $start + (1.0 - $phi) * ($end - $start);
                $x1 = self::distanceAtAngle($points, $template, $a);
            } else {
                $start = $a;
                $a = $b;
                $x1 = $x2;
                $b = $start + $phi * ($end - $start);
                $x2 = self::distanceAtAngle($points, $template, $b);
            }
        }

        return min($x1, $x2);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @param  array<int, array{0: float, 1: float}>  $template
     */
    public static function distanceAtAngle(array $points, array $template, float $theta): float
    {
        $rotated = self::rotateBy($points, $theta);

        return self::pathDistance($rotated, $template);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $candidate
     * @param  array<int, array{0: float, 1: float}>  $template
     */
    public static function greedyCloudMatch(array $candidate, array $template): float
    {
        return self::distanceAtBestAngle($candidate, $template);
    }
}
