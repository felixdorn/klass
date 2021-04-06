<?php

namespace Felix\TailwindClassExtractor\Extractor;

class Extractor
{
    public function extract(string $code): array
    {
        $calls      = [];
        $compiler   = new ComponentCompiler();
        $components = $compiler->compile($code);

        foreach ($components as $component) {
            [$name, $attributes] = $component;

            $calls[] = new BladeComponentCall($name, $attributes);
        }

        return $calls;
    }
}
