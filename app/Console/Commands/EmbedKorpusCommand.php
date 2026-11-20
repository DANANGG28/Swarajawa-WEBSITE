<?php

namespace App\Console\Commands;

use App\Models\Korpus;
use App\Services\Ai\EmbeddingService;
use Illuminate\Console\Command;

class EmbedKorpusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'korpus:embed {--force : Timpa embedding yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and store vector embeddings for korpus records using Google text-embedding-004';

    /**
     * Execute the console command.
     */
    public function handle(EmbeddingService $embeddingService): int
    {
        if (empty(config('ai.gemini_api_key'))) {
            $this->error('GEMINI_API_KEY belum dikonfigurasi. Silakan isi di file .env.');

            return self::FAILURE;
        }

        $query = Korpus::query();
        if (! $this->option('force')) {
            $query->whereNull('embedding');
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('Tidak ada entri korpus yang perlu di-embed.');

            return self::SUCCESS;
        }

        $this->info("Menghasilkan embedding untuk {$total} entri korpus...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(50, function ($records) use ($embeddingService, $bar): void {
            $texts = [];
            foreach ($records as $record) {
                $texts[$record->id] = "{$record->judul}\nKategori: {$record->kategori}\n{$record->konten}";
            }

            $vectors = $embeddingService->batchEmbed($texts);

            foreach ($records as $record) {
                $vector = $vectors[$record->id] ?? null;
                if ($vector !== null) {
                    $record->update(['embedding' => $vector]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Proses embedding korpus selesai.');

        return self::SUCCESS;
    }
}
