<?php

namespace Felix\TailwindClassExtractor\Extractor;

use File;
use Symfony\Component\Finder\SplFileInfo;

class Extractor
{
    public function extractAllComponents(): array
    {
        $calls = collect();

        foreach (config('extractor.views_paths') as $view) {
            $calls->push($this->extractViewsToComponentCalls($view));
        }

        return $calls->flatten()->toArray();
    }

    protected function extractViewsToComponentCalls(string $directory): array
    {
        return collect(File::allFiles($directory))->map(
            fn (SplFileInfo $file) => $this->extractComponentCalls($file->getContents())
        )->flatten()->toArray();
    }

    public function extractComponentCalls(string $code): array
    {
        $calls      = [];
        $compiler   = new ComponentCompiler();
        $components = $compiler->compile($code);

        foreach ($components as $component) {
            [$name, $class, $attributes] = $component;

            $calls[] = new BladeComponentCall($name, $class, $attributes);
        }

        return $calls;
    }
}
