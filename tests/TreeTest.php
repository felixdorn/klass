<?php

use Felix\Klass\ComponentCall;
use Felix\Klass\ComponentDeclaration;
use Felix\Klass\Tree;

it('can visit a node', function () {
    $calls = [
        new ComponentCall(
            new ComponentDeclaration(
                'background',
                'Background',
                '',
                []
            ),
        ),
        new ComponentCall(
            new ComponentDeclaration(
                'background',
                'Background',
                '',
                []
            )
        ),
    ];
    $tree = new Tree($calls);
    $tree->visit(fn (ComponentCall $call) => expect($call)->toBe($calls[0]));
});
