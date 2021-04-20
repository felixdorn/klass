<?php


namespace Felix\TailwindClassExtractor\Processor;


use Felix\TailwindClassExtractor\Extractor\BladeComponentCall;
use Felix\TailwindClassExtractor\Extractor\Extractor;
use Felix\TailwindClassExtractor\Finder\Finder;
use Throwable;

class Processor
{
    protected array $calls;

    public function __construct()
    {
        $this->calls = (new Extractor())->extractAllComponents();
    }

    public function process(): array
    {
        $classes = [];

        foreach ($this->calls as $call) {
            $classes[] = $this->resolveClassesForComponent($call);
        }

        return array_unique($classes);
    }

    protected function resolveClassesForComponent(BladeComponentCall $call): string
    {
        $component = (new Finder())->resolveComponent($call->getName(), $call->getClass());
        $attributes = $component->getDefaults();
        $content = $component->getContent();
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
                $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', $evaluated, $content);
                $content = preg_replace('/\$' . $attribute . '/', $evaluated, $content);
            } catch (Throwable $exception) {
                $content = preg_replace('/{{\s+\$' . $attribute . '\s+}}/', $value, $content);
                $content = preg_replace('/\$' . $attribute . '/', $value, $content);
            }

        }

        preg_match_all('/[^<>"\'`\s]*[^<>"\'`\s:]/m', $content, $matches);

        return collect($matches[0])->filter(function ($match) use ($component, $attributes) {
            foreach ($attributes as $name => $attribute) {
                if (str_contains($match, $attribute)) {
                    return true;
                }
            }

            return false;
        })->unique()->implode(' ');
    }
}
