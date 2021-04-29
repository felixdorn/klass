<?php

namespace Felix\Klass;

use ReflectionClass;
use ReflectionProperty;

class AttributesResolver
{
    /** @var class-string */
    protected string $class;
    protected ReflectionClass $reflection;
    protected array $attributes = [];

    /**
     * @param class-string $class
     */
    public function __construct(string $class)
    {
        $this->class      = $class;
        $this->reflection = new ReflectionClass($this->class);
    }

    /**
     * @param class-string $class
     */
    public static function getAttributes(string $class): array
    {
        return (new self($class))->resolve();
    }

    protected function resolve(): array
    {
        $this->handleConstructorParametersWithReflection();
        $this->handleComponentProperties();
        $this->handlePropertiesDefinedInConstructor();

        return array_filter($this->attributes, function ($attribute) {
            return is_scalar($attribute) || is_null($attribute);
        });
    }

    protected function handleConstructorParametersWithReflection(): void
    {
        if ($this->reflection->getConstructor() === null || $this->reflection->getConstructor()->getNumberOfParameters() === 0) {
            return;
        }

        $parameters = $this->reflection->getConstructor()->getParameters();

        foreach ($parameters as $parameter) {
            $this->attributes[$parameter->getName()] = null;

            if ($parameter->isDefaultValueAvailable()) {
                $this->attributes[$parameter->getName()] = $parameter->getDefaultValue();
            }
        }
    }

    protected function handleComponentProperties(): void
    {
        $properties = collect($this->reflection->getProperties())->mapWithKeys(fn (ReflectionProperty $property) => [$property->getName() => $property]);
        $properties->each(function (ReflectionProperty $property) {
            if (!$this->isPropertyAttribute($property) || array_key_exists($property->getName(), $this->attributes)) {
                return;
            }

            $this->attributes[$property->getName()] = null;
        });

        foreach ($this->reflection->getDefaultProperties() as $name => $default) {
            $property = $properties[$name];

            if (!$this->isPropertyAttribute($property)) {
                continue;
            }

            if (!array_key_exists($name, $this->attributes) || $this->attributes[$name] === null) {
                $this->attributes[$name] = $default;
            }
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

    private function handlePropertiesDefinedInConstructor()
    {
//                try {
//                    $built = Container::resolve($component->getClass(), $attributes->toArray());
//                    $reflection = new ReflectionClass($built);
//
//                    return $attributes->mapWithKeys(function ($value, $key) use ($built, $reflection) {
//                        if (!$reflection->hasProperty($key)) {
//                            return [$key => $value];
//                        }
//if (!$this->propertyHasScalarType($reflection->getProperty($key))) {
//    return [$key => $value];
//}
//
//return [$key => $reflection->getProperty($key)->getValue($built)];
    }
}
