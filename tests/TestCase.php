<?php

namespace Tests;

use Felix\TailwindClassExtractor\TailwindClassExtractorServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [TailwindClassExtractorServiceProvider::class, TestProvider::class];
    }
}
