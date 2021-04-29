<?php

namespace Tests;

use Felix\Klass\KlassServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make('view')->addNamespace('__klass_tests', __DIR__ . '/views');

        foreach (File::allFiles(__DIR__ . '/Components') as $view) {
            $this->app->make('blade.compiler')->component(
                'Tests\\Components\\' . $view->getFilenameWithoutExtension(),
                (string) Str::of($view->getFilenameWithoutExtension())->snake()->replace('_', '-')
            );
        }
    }

    protected function getPackageProviders($app): array
    {
        return [KlassServiceProvider::class];
    }
}
