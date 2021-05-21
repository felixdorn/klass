<?php

use Felix\Klass\CallsCompiler;
use Tests\Components\SimpleVariable;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->compiler = new CallsCompiler();
});

it('returns an empty array if no components are called', function () {
    expect($this->compiler->compile('<h1>Hello world!</h1>'))->toBe([]);
});

it('returns called self closing components', function () {
    $compiled = $this->compiler->compile('<x-simple-variable color="blue" />');
    expect($compiled)->toHaveCount(1);
    expect($compiled[0][0])->toBe('simple-variable');
    expect($compiled[0][1])->toBe(SimpleVariable::class);
    expect($compiled[0][2])->toBe(['color' => 'blue']);
});

it('returns called components', function () {
    $compiled = $this->compiler->compile('<x-simple-variable color="blue">Hello</x-simple-variable>');
    expect($compiled)->toHaveCount(1);
    expect($compiled[0][0])->toBe('simple-variable');
    expect($compiled[0][1])->toBe(SimpleVariable::class);
    expect($compiled[0][2])->toBe(['color' => 'blue']);
});
