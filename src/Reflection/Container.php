<?php

namespace Felix\Klass\Reflection;

use ReflectionClass;
use RuntimeException;

class Container
{
    /**
     * @param class-string $class
     */
    public static function resolve(string $class, array $attributes = []): object
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->getConstructor() === null) {
            return new $class();
        }

        $parameters = $reflection->getConstructor()->getParameters();
        $resolved   = [];

        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter->getName(), $attributes)) {
                $resolved[] = $attributes[$parameter->getName()];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameter->allowsNull()) {
                $resolved[] = null;
                continue;
            }

            if ($parameter->getClass() !== null) {
                $resolved[] = app()->make($parameter->getClass()->getName());
                continue;
            }

            throw new RuntimeException("Unresolvable parameter [{$parameter->getName()}] in [{$parameter->getDeclaringClass()}]");
        }

        return $reflection->newInstanceArgs($resolved);
    }
}
