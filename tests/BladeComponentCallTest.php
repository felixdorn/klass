<?php

use Felix\TailwindClassExtractor\Extractor\BladeComponentCall;

it('can set values', function () {
    $call = new BladeComponentCall('my-component', ['this' => 'that']);

    expect($call->getName())->toBe('my-component');
    expect($call->getAttributes())->toBe([
        'this' => 'that',
    ]);
});
