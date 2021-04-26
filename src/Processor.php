<?php

namespace Felix\Klass;

use Felix\Klass\Component\ComponentCall;
use Felix\Klass\Component\ComponentDeclaration;
use Felix\Klass\Reflection\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class Processor
{
    protected array $calls;

    public function __construct()
    {
        $calls = collect();

        foreach (config('klass.views_paths') as $directory) {
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

        /** @var ComponentCall $call */
        foreach ($this->calls as $call) {
            $classes[] = $this->resolveClassesForComponent($call);
        }

        return array_unique($classes);
    }

    protected function resolveClassesForComponent(ComponentCall $call): string
    {
        $component  = (new Finder($call->getName(), $call->getClass()))->resolve();
        $attributes = $this->mergeAttributes($component, $call);
        $content    = $component->getContent();

        dump($attributes);
        foreach ($attributes as $attribute => $value) {
            try {
                $evaluated = eval("return $value");
            } catch (Throwable $exception) {
            }

            $evaluated ??= $value;

            if (!is_scalar($evaluated) && !is_null($evaluated)) {
                $evaluated = '[invalid]';
            }

            $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', (string) $evaluated, $content);
            /* @phpstan-ignore-next-line */
            $content = str_replace('$' . $attribute, $evaluated, $content);
        }

        preg_match_all('/[^<>"\'`\s]*[^<>"\'`\s:]/m', $content, $matches);

        return implode(' ', $matches[0]);
    }

    protected function mergeAttributes(ComponentDeclaration $component, ComponentCall $call): array
    {
        return collect($component->getAttributes())
            ->mapWithKeys(function ($key) use ($call, $component) {
                if ($call->hasAttribute($key)) {
                    return [$key => $call->getAttribute($key)];
                }

                if ($component->hasDefault($key)) {
                    return [$key => $component->getDefault($key)];
                }

                return [$key => 'empty value'];
            })->pipe(function (Collection $attributes) use ($component) {
                try {
                    $built = Container::resolve($component->getClass(), $attributes->toArray());
                    $reflection = new ReflectionClass($built);

                    return $attributes->mapWithKeys(function ($value, $key) use ($built, $reflection) {
                        if (!$reflection->hasProperty($key)) {
                            return [$key => $value];
                        }

                        if (!$this->propertyHasScalarType($reflection->getProperty($key))) {
                            return [$key => $value];
                        }

                        return [$key => $reflection->getProperty($key)->getValue($built)];
                    });
                } catch (BindingResolutionException $e) {
                }

                return $attributes;
            })->toArray();
    }

    protected function propertyHasScalarType(ReflectionProperty $property): bool
    {
        // If it might not be scalar then it's not scalar
        if ($property->getType() === null) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        $type = $property->getType()->getName();

        // "resource (closed)" might be irrelevant here.
        return !in_array($type, ['array', 'object', 'resource', 'resource (closed)']);
    }
}
