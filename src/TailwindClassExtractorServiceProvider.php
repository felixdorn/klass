<?php

namespace Felix\TailwindClassExtractor;

use Felix\TailwindClassExtractor\Commands\TailwindExtractCommand;
use Illuminate\Support\ServiceProvider;

class TailwindClassExtractorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            TailwindExtractCommand::class,
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/extractor.php', 'extractor');

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/extractor.php' => config_path('extractor.php'),
        ], 'extractor-config');
    }
}
