<?php

namespace Felix\Klass;

use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Klass
{
    protected static ?Klass $uniqueInstance = null;

    public static function getInstance(): self
    {
        if (self::$uniqueInstance === null) {
            self::$uniqueInstance = new self();
        }

        return self::$uniqueInstance;
    }

    public function tree(): Tree
    {
        $viewsPath = config('klass.views_paths');
        $calls     = [];
        foreach ($viewsPath as $directory) {
            $calls[] = array_map(
                fn (SplFileInfo $view) => $this->extractCalls($view->getContents()),
                $this->allFiles($directory)
            );
        }

        return new Tree(Arr::flatten($calls));
    }

    protected function extractCalls(string $code): array
    {
        $componentCompiler = new ComponentCallsCompiler();
        $components        = $componentCompiler->compile($code);

        return array_map(function (array $component) {
            [$name, $class, $attributes] = $component;

            return new ComponentCall(
                ComponentDeclarationFactory::findOrCreate($name, $class),
                $attributes
            );
        }, $components);
    }

    protected function allFiles(string $directory): array
    {
        return iterator_to_array(
            (new Finder())
                ->files()
                ->name('*.blade.php')
                ->in($directory)
        );
    }
}
