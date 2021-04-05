<?php

use Felix\TailwindClassExtractor\BladeComponentCall;
use Illuminate\View\ComponentAttributeBag;

it('can set values', function () {
    $call = new BladeComponentCall('my-component', new ComponentAttributeBag([
        'this' => 'that'
    ]));

    expect($call->getName())->toBe('my-component');
    expect($call->getAttributes()->getAttributes())->toBe([
        'this' => 'that'
    ]);
});
