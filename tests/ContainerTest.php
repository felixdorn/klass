<?php

use Felix\Klass\Reflection\Container;
use Illuminate\View\Factory;
use Tests\TestCase;

uses(TestCase::
class);

class ShouldBeResolved
{
    public string $foo;
    public string $bar;
    public ?string $faz;
    public Factory $factory;

    public function __construct(string $foo, string $bar = 'baz', ?string $faz, Factory $factory)
    {
        $this->foo     = $foo;
        $this->bar     = $bar;
        $this->faz     = $faz;
        $this->factory = $factory;
    }
}

class ShouldNotBeResolved
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

it('can resolve a component', function () {
    /** @var ShouldBeResolved $resolved */
    $resolved = Container::resolve(ShouldBeResolved::class, [
        'foo' => 'bar',
    ]);

    expect($resolved->foo)->toBe('bar');
    expect($resolved->bar)->toBe('baz');
    expect($resolved->faz)->toBeNull();
    expect($resolved->factory)->toBe(app('view'));
});

it('can not resolve an unresolvable parameter', function () {
    Container::resolve(ShouldNotBeResolved::class);
})->throws(RuntimeException::class);
