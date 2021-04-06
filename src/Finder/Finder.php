<?php

namespace Felix\TailwindClassExtractor\Finder;

class Finder
{
    public function getAllComponents(): array
    {
        $classComponentAliases = app('blade.compiler')->getClassComponentAliases();
        $components            = [];

        unset($classComponentAliases['dynamic-component']);

        foreach ($classComponentAliases as $name => $class) {
            $resolver     = new ComponentAttributesResolver($name, $class);
            $components[] = $resolver->resolve();
        }

        return $components;
    }
}
