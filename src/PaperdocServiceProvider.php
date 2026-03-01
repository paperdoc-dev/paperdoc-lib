<?php

declare(strict_types=1);

namespace Paperdoc;

use Illuminate\Support\ServiceProvider;
use Paperdoc\Contracts\LlmAugmenterInterface;
use Paperdoc\Contracts\OcrProcessorInterface;
use Paperdoc\Llm\LlmAugmenter;
use Paperdoc\Ocr\OcrManager;
use Paperdoc\Ocr\TesseractOcrProcessor;
use Paperdoc\Console\BuildDictionaryCommand;
use Paperdoc\Console\TrainNgramCommand;
use Paperdoc\Support\DocumentManager;

class PaperdocServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paperdoc.php', 'paperdoc');

        $this->commands([
            BuildDictionaryCommand::class,
            TrainNgramCommand::class,
        ]);

        $this->app->singleton(DocumentManager::class);
        $this->app->alias(DocumentManager::class, 'paperdoc');

        $this->app->singleton(OcrProcessorInterface::class, function ($app) {
            $config = $app['config']->get('paperdoc.ocr.tesseract', []);

            return new TesseractOcrProcessor(
                binary: $config['binary'] ?? 'tesseract',
                extraOptions: $config['options'] ?? [],
            );
        });

        $this->app->singleton(OcrManager::class, function ($app) {
            $config = $app['config']->get('paperdoc.ocr', []);

            $poolSize = $config['pool_size'] ?? 0;
            if ($poolSize === 'auto') {
                $poolSize = 0;
            }

            return new OcrManager(
                processor: $app->make(OcrProcessorInterface::class),
                language: $config['language'] ?? 'auto',
                minTextRatio: (float) ($config['min_text_ratio'] ?? 0.1),
                poolSize: (int) $poolSize,
                processTimeout: (int) ($config['process_timeout'] ?? 60),
            );
        });

        $this->app->singleton(LlmAugmenterInterface::class, function ($app) {
            $config = $app['config']->get('paperdoc.llm', []);

            return new LlmAugmenter($config);
        });
    }

    public function boot(): void
    {
        $this->configurePublishing();
    }

    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/paperdoc.php' => config_path('paperdoc.php'),
        ], 'paperdoc-config');
    }
}
