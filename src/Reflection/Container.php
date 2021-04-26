<?php

namespace Felix\Klass\Reflection;

use ReflectionClass;
use RuntimeException;

class Container
{
    /**
     * @param class-string $class
     *
     * @return mixed|object
     */
    public static function resolve(string $class, array $with = [])
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->getConstructor() === null || $reflection->getConstructor()->getNumberOfRequiredParameters() === 0) {
            return new $class();
        }

        $parameters         = $reflection->getConstructor()->getParameters();
        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            // If it's a class (probably a model passed as an argument)
            // $with would contain the variable referencing this object like "$user"
            // So we only resolve the parameter using $with if the type is not an object
            if (!$parameter->getClass() && array_key_exists($parameter->getName(), $with)) {
                $resolvedParameters[] = $with[$parameter->getName()];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolvedParameters[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameter->allowsNull()) {
                $resolvedParameters[] = null;
                continue;
            }

            if ($parameter->getClass() === null) {
                throw new RuntimeException(sprintf('Unresolvable parameter %s in %s', $parameter->getName(), $class));
            }

            $resolvedParameters[] = app($parameter->getClass()->getName());
        }

        return $reflection->newInstanceArgs($resolvedParameters);
    }
}
