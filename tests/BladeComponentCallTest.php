<?php

use Felix\TailwindClassExtractor\Component\ComponentCall;
use Tests\TestCase;

uses(TestCase::class);

class MyComponent
{
}

it('can set values', function () {
    $call = new ComponentCall('my-component', MyComponent::class, ['this' => 'that']);

    expect($call->getName())->toBe('my-component');
    expect($call->getAttributes())->toBe([
        'this' => 'that',
    ]);
});
