<?php

use Felix\TailwindClassExtractor\Extractor\BladeComponentCall;


class MyComponent
{

}

it('can set values', function () {
    $call = new BladeComponentCall('my-component', MyComponent::class, ['this' => 'that']);

    expect($call->getName())->toBe('my-component');
    expect($call->getAttributes())->toBe([
        'this' => 'that',
    ]);
});
