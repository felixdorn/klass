<?php

namespace Felix\TailwindClassExtractor\Finder;

use Illuminate\View\View;
use ReflectionClass;

class BladeComponentDeclaration
{
    protected string $name;
    protected array $attributes;
    protected array $defaults;
    protected string $class;

    public function __construct(string $name, string $class, array $attributes, array $defaults = [])
    {
        $this->name       = $name;
        $this->class      = $class;
        $this->attributes = $attributes;
        $this->defaults   = $defaults;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContent(): string
    {
        $ref = new ReflectionClass($this->class);
        /** @var View $component */
        $component = $ref->newInstanceWithoutConstructor()->render();

        if (!$component instanceof View) {
            // TODO: Do some smart stuff here maybe
            return '';
        }

        return file_get_contents($component->getPath());
    }
}
