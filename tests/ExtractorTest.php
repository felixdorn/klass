<?php

use Felix\TailwindClassExtractor\Extractor\BladeComponentCall;
use Felix\TailwindClassExtractor\Extractor\Extractor;
use Tests\Fixtures\app\View\Components\Background;

beforeEach(function () {
    $this->extractor = new Extractor();
});

it('can extract a self closing component', function () {
    $extracted = $this->extractor->extract('<x-tests-background color="red" />');

    expect($extracted)->toMatchObjects([
        new BladeComponentCall('tests-background', Background::class, ['color' => 'red']),
    ]);
});

it('can extract a component', function () {
    $extracted = $this->extractor->extract('<x-tests-background color="blue" fill />');

    expect($extracted)->toMatchObjects([
        new BladeComponentCall('tests-foreground', Background::class, ['color' => 'blue', 'fill' => 'true']),
    ]);
});

it('returns an empty array when no components are called', function () {
    expect($this->extractor->extract(''))->toBeEmpty();
    expect($this->extractor->extract('<h1>Some <span>normal</span> <abbr title="HyperText Markup Language">HTML</abbr></h1>'))->toBeEmpty();
});
