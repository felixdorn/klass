<?php

namespace Tests;

use Illuminate\Support\ServiceProvider;
use Tests\Fixtures\app\View\Components\Background;

class TestProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/Fixtures/resources/views', 'tests');
        $this->loadViewComponentsAs('tests', [
            Background::class,
        ]);
    }
}
