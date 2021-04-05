<?php

use Pest\Expectations\Expectation;

expect()->extend('toMatchObjectDeeply', function (object $object) {
    /* @var Expectation $this */
    $this->toBeInstanceOf(get_class($object));

    $reflection = new ReflectionObject($this->value);
    $comparison = new ReflectionObject($object);

    $reflectionProperties = $reflection->getProperties();

    foreach ($reflectionProperties as $reflectionProperty) {
        $reflectionProperty->setAccessible(true);

        expect($comparison->hasProperty($reflectionProperty->getName()))->toBeTrue();

        $comparedProperty = $comparison->getProperty($reflectionProperty->getName());
        $comparedProperty->setAccessible(true);

        if ($reflectionProperty->isInitialized($this->value) && !$comparedProperty->isInitialized($object)) {
            expect(false)->toBe("property $reflectionProperty->name should be initialized");
        }

        if ($reflectionProperty->isInitialized($this->value)) {
            $value = $reflectionProperty->getValue($this->value);

            $compared = $comparedProperty->getValue($object);
            if (is_object($value)) {
                expect($value)->toMatchObjectDeeply($compared);
            } else {
                expect($value)->toBe($compared);
            }
        }
    }
});

expect()->extend('toMatchObjects', function (iterable $iterable) {
    /* @var Expectation $this */
    foreach ($iterable as $k => $value) {
        $this->toHaveKey($k);
        expect($this->value[$k])->toMatchObjectDeeply($iterable[$k]);
    }
});
