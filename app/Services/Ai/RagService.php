<?php

namespace App\Services\Ai;

use App\Models\Korpus;
use App\Support\TextSimilarity;

/**
 * Chatbot RAG dengan guardrail (FR-9).
 *
 * Menjawab HANYA berdasarkan korpus yang tersimpan. Bila tidak ada dokumen
 * yang cukup relevan, chatbot menolak secara eksplisit alih-alih mengarang.
 * Pendekatan retrieval berbasis kemiripan kata (tanpa LLM generatif bebas),
 * konsisten dengan peringatan PRD §6C & §9 soal risiko halusinasi.
 */
class RagService
{
    public const THRESHOLD = 0.5;

    /**
     * @return array{dijawab: bool, jawaban: string, sumber: array<int, array<string, ?string>>, skor_tertinggi: float}
     */
    public function ask(string $question, int $limit = 3): array
    {
        $tokens = array_values(array_unique(TextSimilarity::tokens($question)));

        if ($tokens === []) {
            return $this->refuse();
        }

        $scored = Korpus::query()
            ->get()
            ->map(function (Korpus $item) use ($tokens): array {
                $docTokens = array_unique(array_merge(
                    TextSimilarity::tokens($item->judul),
                    TextSimilarity::tokens($item->kategori),
                    TextSimilarity::tokens($item->konten),
                ));

                // Cakupan (coverage): proporsi kata kunci pertanyaan yang ada di dokumen.
                $intersection = count(array_intersect($tokens, $docTokens));
                $coverage = $intersection / max(1, count($tokens));

                return [
                    'item' => $item,
                    'score' => $coverage,
                ];
            })
            ->sortByDesc('score')
            ->values();

        $relevant = $scored
            ->filter(fn (array $row): bool => $row['score'] >= self::THRESHOLD)
            ->take($limit);

        if ($relevant->isEmpty()) {
            return $this->refuse();
        }

        return [
            'dijawab' => true,
            'jawaban' => $relevant->map(fn (array $row): string => $row['item']->konten)->implode("\n\n"),
            'sumber' => $relevant->map(fn (array $row): array => [
                'judul' => $row['item']->judul,
                'kategori' => $row['item']->kategori,
                'sumber' => $row['item']->sumber,
            ])->all(),
            'skor_tertinggi' => round((float) $relevant->first()['score'], 3),
        ];
    }

    /**
     * @return array{dijawab: bool, jawaban: string, sumber: array<int, mixed>, skor_tertinggi: float}
     */
    private function refuse(): array
    {
        return [
            'dijawab' => false,
            'jawaban' => 'Nyuwun pangapunten, pitakon menika wonten ing sanjabane korpus pasinaon ingkang kasedhiya. Kula namung saged mbiyantu babagan basa lan budaya Jawa ingkang wonten ing materi.',
            'sumber' => [],
            'skor_tertinggi' => 0.0,
        ];
    }
}
