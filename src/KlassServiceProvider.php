<?php

namespace Felix\Klass;

use Felix\Klass\Commands\KlassExtractCommand;
use Illuminate\Support\ServiceProvider;

class KlassServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            KlassExtractCommand::class,
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
