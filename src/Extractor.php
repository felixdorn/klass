<?php

namespace Felix\Klass;

use Felix\Klass\Component\ComponentCall;
use Felix\Klass\Component\ComponentCompiler;

class Extractor
{
    public static function extractCalls(string $code): array
    {
        $calls      = [];
        $compiler   = new ComponentCompiler();
        $components = $compiler->compile($code);

        foreach ($components as $component) {
            [$name, $class, $attributes] = $component;

            $calls[] = new ComponentCall($name, $class, array_combine(
                array_map(
                    fn (string $attribute) => str_starts_with($attribute, ':') ? substr($attribute, 1) : $attribute,
                    array_keys($attributes)
                ),
                array_values($attributes)
            ) ?: []);
        }

        return $calls;
    }
}
