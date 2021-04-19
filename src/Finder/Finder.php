<?php

namespace Felix\TailwindClassExtractor\Finder;

class Finder
{
    public function resolveComponent(string $name, string $class): BladeComponentDeclaration
    {
        $resolver = new ComponentAttributesResolver($name, $class);
        return $resolver->resolve();
    }
}
