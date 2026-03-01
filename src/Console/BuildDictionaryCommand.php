<?php

declare(strict_types=1);

namespace Paperdoc\Console;

use Illuminate\Console\Command;
use Paperdoc\Ocr\PostProcessing\SpellCorrector;

class BuildDictionaryCommand extends Command
{
    protected $signature = 'paperdoc:build-dictionary
        {sources* : Text files or directories to read}
        {--output=storage/paperdoc/dictionary.txt : Output dictionary path}
        {--min-freq=2 : Minimum word frequency to keep}
        {--min-length=2 : Minimum word length}
        {--append : Append to existing dictionary instead of overwriting}';

    protected $description = 'Build a spell-correction dictionary from text files for OCR Layer 2';

    public function handle(): int
    {
        $sources = $this->argument('sources');
        $output = $this->option('output');
        $minFreq = (int) $this->option('min-freq');
        $minLength = (int) $this->option('min-length');
        $append = $this->option('append');

        $corrector = new SpellCorrector;

        if ($append && file_exists($output)) {
            $corrector->loadFile($output);
            $this->info("Dictionnaire existant chargé : {$output}");
        }

        $files = $this->resolveFiles($sources);

        if (empty($files)) {
            $this->error('Aucun fichier texte trouvé dans les sources fournies.');

            return self::FAILURE;
        }

        $this->info(sprintf('Traitement de %d fichier(s)…', count($files)));
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content !== false) {
                $corrector->trainFromText($content);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $outputDir = dirname($output);
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $sizeBefore = $corrector->getDictionarySize();
        $corrector->filterByFrequency($minFreq, $minLength);
        $sizeAfter = $corrector->getDictionarySize();

        $corrector->saveDictionary($output);

        $this->info("Dictionnaire généré : {$output}");
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Mots collectés', number_format($sizeBefore)],
                ["Après filtrage (freq≥{$minFreq}, len≥{$minLength})", number_format($sizeAfter)],
                ['Fichier', realpath($output) ?: $output],
            ],
        );

        $this->newLine();
        $this->comment('Pour activer Layer 2, ajoutez dans config/paperdoc.php :');
        $this->line("  'spell_correction' => ['enabled' => true, 'dictionary' => '{$output}']");

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
}
