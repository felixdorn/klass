<?php

namespace Tests;

use Felix\Klass\KlassServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->setUpTheTestEnvironment();

        $this->app->make('blade.compiler')->component(
            Components\Background::class,
            'background'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [KlassServiceProvider::class];
    }
}
