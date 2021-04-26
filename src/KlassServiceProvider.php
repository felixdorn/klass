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

        $this->mergeConfigFrom(__DIR__ . '/../config/klass.php', 'klass');

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/klass.php' => config_path('klass.php'),
        ], 'klass-config');
    }
}
