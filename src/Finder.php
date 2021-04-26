<?php

namespace Felix\Klass;

use Felix\Klass\Component\ComponentDeclaration;
use ReflectionClass;
use ReflectionProperty;

class Finder
{
    protected string $component;
    /** @var class-string */
    protected string $class;
    protected ReflectionClass $reflection;
    protected array $attributes = [];
    protected array $defaults   = [];

    /**
     * @param class-string $class
     */
    public function __construct(string $component, string $class)
    {
        $this->component  = $component;
        $this->class      = $class;
        $this->reflection = new ReflectionClass($this->class);
    }

    public function resolve(): ComponentDeclaration
    {
        $this->handleConstructorParametersWithReflection();
        $this->handleComponentProperties();

        $this->defaults = array_filter($this->defaults, 'is_scalar');

        return new ComponentDeclaration($this->component, $this->class, array_unique($this->attributes), $this->defaults);
    }

    protected function handleConstructorParametersWithReflection(): void
    {
        if ($this->reflection->getConstructor() === null || $this->reflection->getConstructor()->getNumberOfParameters() === 0) {
            return;
        }

        $parameters = $this->reflection->getConstructor()->getParameters();

        foreach ($parameters as $parameter) {
            $this->attributes[] = $parameter->getName();

            if (!$parameter->isDefaultValueAvailable()) {
                continue;
            }

            if ($parameter->getDefaultValue() !== null) {
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
