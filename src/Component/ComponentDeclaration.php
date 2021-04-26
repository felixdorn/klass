<?php

namespace Felix\Klass\Component;

use Illuminate\View\Component;
use Illuminate\View\View;
use ReflectionClass;

class ComponentDeclaration
{
    protected string $name;
    protected array $attributes;
    protected array $defaults;
    /**
     * @var class-string
     */
    protected string $class;

    /**
     * @param class-string $class
     */
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
        /** @var ReflectionClass<Component> $ref */
        $ref       = new ReflectionClass($this->class);
        $component = $ref->newInstanceWithoutConstructor()->render();

        // Support for inline components
        if (gettype($component) === 'string') {
            return $component;
        }

        // Support for closures in render() should go there
        if (!$component instanceof View) {
            // TODO: Do some smart stuff here maybe
            return '';
        }

        return file_get_contents($component->getPath()) ?: '';
    }
}
