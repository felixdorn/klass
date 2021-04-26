<?php

use Felix\Klass\Component\ComponentCall;
use Felix\Klass\Extractor;
use Tests\Fixtures\app\View\Components\Background;
use Tests\TestCase;

uses(TestCase::class);

it('can extract a self closing component', function () {
    $extracted = Extractor::extractCalls('<x-tests-background color="red" />');

    expect($extracted)->toMatchObjects([
        new ComponentCall('tests-background', Background::class, ['color' => 'red']),
    ]);
});

it('can extract a component', function () {
    $extracted = Extractor::extractCalls('<x-tests-background color="blue" fill />');

    expect($extracted)->toMatchObjects([
        new ComponentCall('tests-background', Background::class, ['color' => 'blue', 'fill' => 'true']),
    ]);
});

it('returns an empty array when no components are called', function () {
    expect(Extractor::extractCalls(''))->toBeEmpty();
    expect(Extractor::extractCalls('<h1>Some <span>normal</span> <abbr title="HyperText Markup Language">HTML</abbr></h1>'))->toBeEmpty();
});
