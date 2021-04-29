<?php

use Felix\Klass\Call;
use Felix\Klass\Calls;
use Felix\Klass\Declaration;
use Felix\Klass\Visitors\CallableVisitor;

it('can visit a node', function () {
    $calls = [
        new Call(
            new Declaration(
                'simple-variable',
                'SimpleVariable',
                '',
                []
            ),
        ),
        new Call(
            new Declaration(
                'simple-variable',
                'SimpleVariable',
                '',
                []
            )
        ),
    ];
    $tree = new Calls($calls);
    $tree->addVisitor(new CallableVisitor(function (Call $call) use ($calls) {
        expect($call)->toBe($calls[0]);
    }));
    $tree->visit();
});
