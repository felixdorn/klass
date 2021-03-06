<?php

namespace Felix\Klass;

use Closure;
use Felix\Klass\Support\MalleableSlots;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;
use ReflectionClass;

class DeclarationFactory
{
    /** @var Declaration[] */
    protected static array $declarations = [];

    public static function declarations(): array
    {
        return static::$declarations;
    }

    /** @param class-string $class */
    public static function findOrCreate(string $name, string $class): Declaration
    {
        if (static::has($name)) {
            return static::find($name);
        }

        return static::add(static::create($name, $class));
    }

    public static function has(string $name): bool
    {
        return array_key_exists($name, static::$declarations);
    }

    public static function find(string $name): Declaration
    {
        return static::$declarations[$name];
    }

    public static function add(Declaration $declaration): Declaration
    {
        static::$declarations[$declaration->getName()] = $declaration;

        return $declaration;
    }

    /** @param class-string $class */
    public static function create(string $name, string $class): Declaration
    {
        return new Declaration(
            $name,
            $class,
            static::getComponentContents($name, $class),
            AttributesResolver::getAttributes($class)
        );
    }

    /** @param class-string $class */
    protected static function getComponentContents(string $name, string $class): string
    {
        if (str_contains($name, '-')) {
            return static::getComponentContentsWithReflection($name, $class);
        }
        $name = sprintf(
            '%s%s.blade.php',
            resource_path('views/components/'),
            str_replace('.', DIRECTORY_SEPARATOR, $name)
        );

        if (file_exists($name)) {
            return file_get_contents($name) ?: '';
        }

        return static::getComponentContentsWithReflection($name, $class);
    }

    /** @param class-string $class */
    protected static function getComponentContentsWithReflection(string $name, string $class): string
    {
        /** @var ReflectionClass<Component> $ref */
        $ref       = new ReflectionClass($class);
        $component = $ref->newInstanceWithoutConstructor()->render();

        // Support for inline components
        if (is_string($component)) {
            return $component;
        }

        if ($component instanceof View) {
            return file_get_contents($component->getPath()) ?: '';
        }

        if ($component instanceof Closure) {
            $componentData = [
                'componentName'   => $name,
                'attributes'      => new ComponentAttributeBag(),
                'slot'            => new HtmlString(),
                '__laravel_slots' => new MalleableSlots(),
            ];

            $called = $component($componentData);

            if ($called instanceof View) {
                return file_get_contents($called->getPath()) ?: '';
            }
        }

        return '';
    }
}
