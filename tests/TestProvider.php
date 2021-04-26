<?php

namespace Tests;

use Illuminate\Support\ServiceProvider;
use Tests\Fixtures\app\View\Components\Background;

class TestProvider extends ServiceProvider
{
    public function register(): void
    {
        // not passing the variable directly prevents Laravel Idea from autocompleting it.
        $components = [Background::class];
        $this->loadViewsFrom(__DIR__ . '/Fixtures/resources/views', 'tests');
        $this->loadViewComponentsAs('tests', $components);
    }
}
