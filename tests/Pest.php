<?php

use Felix\Klass\Calls;
use Felix\Klass\Commands\KlassExtractCommand;
use Felix\Klass\Klass;
use Felix\Klass\Visitors\CallableVisitor;
use Tests\TestCase;

uses(TestCase::class)->in('Features/');

expect()->extend('toHaveExtractedClasses', function (array $classes) {
    $calls = new Calls(
        Klass::getInstance()->extractCalls($this->value)
    );
    $command = new KlassExtractCommand();
    $calls->addVisitor(new CallableVisitor([$command, 'visit']));
    $calls->visit();

    expect($command->getClasses())->toBe($classes);
});
