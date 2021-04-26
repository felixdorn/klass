<?php

use Felix\Klass\Component\ComponentDeclaration;
use Felix\Klass\Finder;
use Illuminate\View\Component;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config()->set('klass', [
        'components_paths' => [
            __DIR__ . '/Fixtures/resources/views/components',
        ],

        'views_paths' => [
            __DIR__ . '/Fixtures/resources/views',
        ],
    ]);
});

class _Background extends Component
{
    public string $color;

    public function __construct(string $color = 'blue')
    {
        $this->color = $color;
    }

    public function render()
    {
        return view('...');
    }
}

class _BackgroundWithPrivateProperty
{
    protected string $type;
    private string $color;
}

class _BackgroundWithStaticProperty
{
    public static $foo;
    protected static $bar;
    private static $baz;
}

it('can resolve component constructor parameters', function () {
    $resolver = new Finder('background', _Background::class);

    expect($resolver->resolve())->toMatchObjectDeeply(new ComponentDeclaration('background', _Background::class, ['color'], ['color' => 'blue']));
});

it('does not resolve protected / private properties', function () {
    $resolver = new Finder('background', _BackgroundWithPrivateProperty::class);

    expect($resolver->resolve())->toMatchObjectDeeply(new ComponentDeclaration('background', _BackgroundWithPrivateProperty::class, [], []));
});
it('does not resolve static properties', function () {
    $resolver = new Finder('background', _BackgroundWithStaticProperty::class);

    expect($resolver->resolve())->toMatchObjectDeeply(new ComponentDeclaration('background', _BackgroundWithStaticProperty::class, [], []));
});
