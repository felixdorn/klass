<?php

namespace Felix\TailwindClassExtractor;

use Felix\TailwindClassExtractor\Component\ComponentCall;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class Processor
{
    protected array $calls;

    public function __construct()
    {
        $calls = collect();

        foreach (config('extractor.views_paths') as $directory) {
            $calls->push(
                collect(File::allFiles($directory))->map(
                    fn (SplFileInfo $file) => Extractor::extractCalls($file->getContents())
                )->flatten()->toArray()
            );
        }

        $this->calls = $calls->flatten()->toArray();
    }

    public function process(): array
    {
        $classes = [];

        foreach ($this->calls as $call) {
            $classes[] = $this->resolveClassesForComponent($call);
        }

        return array_unique($classes);
    }

    protected function resolveClassesForComponent(ComponentCall $call): string
    {
        $component  = (new Finder($call->getName(), $call->getClass()))->resolve();
        $attributes = $component->getDefaults();
        $content    = $component->getContent();
        foreach ($call->getAttributes() as $name => $value) {
            $attributes[str_starts_with($name, ':') ? substr($name, 1) : $name] = $value;
        }

        foreach ($attributes as $attribute => $value) {
            try {
                $evaluated = eval("return $value");

                if ($evaluated === null) {
                    $evaluated = $value;
                }

                $attributes[$attribute] = $value;
            } catch (Throwable $exception) {
            }

            /** @phpstan-ignore-next-line */
            $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', $evaluated ?? $value, $content);
            /** @phpstan-ignore-next-line */
            $content = preg_replace('/\$' . $attribute . '/', $evaluated ?? $value, $content);
        }

        /* @phpstan-ignore-next-line */
        preg_match_all('/[^<>"\'`\s]*[^<>"\'`\s:]/m', $content, $matches);

        return collect($matches[0])->filter(function ($match) use ($attributes) {
            foreach ($attributes as $name => $attribute) {
                if (str_contains($match, $attribute)) {
                    return true;
                }
            }

            return false;
        })->unique()->implode(' ');
    }
}
