<?php

declare(strict_types=1);

namespace Paperdoc\Console;

use Illuminate\Console\Command;
use Paperdoc\Ocr\PostProcessing\NgramScorer;

class TrainNgramCommand extends Command
{
    protected $signature = 'paperdoc:train-ngram
        {sources* : Text files or directories to read}
        {--output=storage/paperdoc/ngram-model.json : Output model path}
        {--append : Append to existing model instead of overwriting}';

    protected $description = 'Train an n-gram language model from text files for OCR Layer 3';

    public function handle(): int
    {
        $sources = $this->argument('sources');
        $output = $this->option('output');
        $append = $this->option('append');

        if ($append && file_exists($output)) {
            $scorer = NgramScorer::loadModel($output);
            $this->info("Modèle existant chargé : {$output}");
        } else {
            $scorer = new NgramScorer;
        }

        $files = $this->resolveFiles($sources);

        if (empty($files)) {
            $this->error('Aucun fichier texte trouvé dans les sources fournies.');

            return self::FAILURE;
        }

        $this->info(sprintf('Entraînement sur %d fichier(s)…', count($files)));
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content !== false) {
                $scorer->train($content);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $outputDir = dirname($output);
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $scorer->saveModel($output);

        $stats = $scorer->getStats();

        $this->info("Modèle n-gram généré : {$output}");
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Unigrammes', number_format($stats['unique_unigrams'])],
                ['Bigrammes', number_format($stats['unique_bigrams'])],
                ['Total tokens', number_format($stats['total_unigrams'])],
                ['Total bigrammes', number_format($stats['total_bigrams'])],
                ['Taille fichier', self::formatBytes(filesize($output) ?: 0)],
            ],
        );

        $this->newLine();
        $this->comment('Pour activer Layer 3, ajoutez dans config/paperdoc.php :');
        $this->line("  'ngram' => ['enabled' => true, 'model_path' => '{$output}']");

        return self::SUCCESS;
    }

    /** @return string[] */
    private function resolveFiles(array $sources): array
    {
        $files = [];

        foreach ($sources as $source) {
            if (is_dir($source)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && in_array($file->getExtension(), ['txt', 'md', 'text', 'csv'], true)) {
                        $files[] = $file->getPathname();
                    }
                }
            } elseif (is_file($source)) {
                $files[] = $source;
            }
        }

        return $files;
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 1) . ' ' . $units[$i];
    }
}
