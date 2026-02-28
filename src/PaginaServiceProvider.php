<?php

declare(strict_types=1);

namespace Pagina;

use Illuminate\Support\ServiceProvider;
use Pagina\Support\DocumentManager;

class PaginaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pagina.php', 'pagina');

        $this->app->singleton(DocumentManager::class);
        $this->app->alias(DocumentManager::class, 'pagina');
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
            __DIR__ . '/../config/pagina.php' => config_path('pagina.php'),
        ], 'pagina-config');
    }
}
