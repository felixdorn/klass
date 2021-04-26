<?php

namespace Tests;

use Felix\Klass\KlassServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [KlassServiceProvider::class, TestProvider::class];
    }
}
