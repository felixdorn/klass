<?php

namespace Felix\TailwindClassExtractor\Finder;

use ReflectionClass;
use ReflectionProperty;

class ComponentAttributesResolver
{
    protected string $component;
    protected string $class;
    protected ReflectionClass $reflection;
    protected array $attributes = [];
    protected array $defaults   = [];

    public function __construct(string $component, string $class)
    {
        $this->component  = $component;
        $this->class      = $class;
        $this->reflection = new ReflectionClass($this->class);
    }

    public function resolve(): BladeComponentDeclaration
    {
        $this->handleConstructorParameters();
        $this->handleComponentProperties();

        return new BladeComponentDeclaration($this->component, array_unique($this->attributes), array_unique($this->defaults));
    }

    protected function handleConstructorParameters(): void
    {
        if ($this->reflection->getConstructor() === null || $this->reflection->getConstructor()->getNumberOfParameters() === 0) {
            return;
        }

        $parameters = $this->reflection->getConstructor()->getParameters();

        foreach ($parameters as $parameter) {
            $this->attributes[] = $parameter->getName();

            if ($parameter->isDefaultValueAvailable()) {
                $this->defaults[$parameter->getName()] = $parameter->getDefaultValue();
            }
        }
    }

    protected function handleComponentProperties(): void
    {
        $properties = collect($this->reflection->getProperties())->mapWithKeys(fn (ReflectionProperty $property) => [$property->getName() => $property]);

        $properties->each(function (ReflectionProperty $property) {
            if (!$this->isPropertyAttribute($property)) {
                return;
            }

            $this->attributes[] = $property->getName();
        });

        foreach ($this->reflection->getDefaultProperties() as $name => $default) {
            $property = $properties[$name];

            if (!$this->isPropertyAttribute($property)) {
                continue;
            }

            $this->defaults[$name] = $default;
        }
    }

    protected function isPropertyAttribute(ReflectionProperty $property): bool
    {
        if (!$property->isPublic()) {
            return false;
        }

        if ($property->isStatic()) {
            return false;
        }

        if (in_array($property->getName(), ['componentName', 'attributes'])) {
            return false;
        }

        return true;
    }
}
