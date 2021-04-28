<?php

use Felix\Klass\CallsCompiler;
use Tests\Components\Background;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->compiler = new CallsCompiler();
});

it('returns an empty array if no components are called', function () {
    expect($this->compiler->compile('<h1>Hello world!</h1>'))->toBe([]);
});

it('returns called self closing components', function () {
    $compiled = $this->compiler->compile('<x-background color="blue" />');
    expect($compiled)->toHaveCount(1);
    expect($compiled[0][0])->toBe('background');
    expect($compiled[0][1])->toBe(Background::class);
    expect($compiled[0][2])->toBe(['color' => 'blue']);
});

it('returns called components', function () {
    $compiled = $this->compiler->compile('<x-background color="blue">Hello</x-background>');
    expect($compiled)->toHaveCount(1);
    expect($compiled[0][0])->toBe('background');
    expect($compiled[0][1])->toBe(Background::class);
    expect($compiled[0][2])->toBe(['color' => 'blue']);
});
