<?php

namespace Felix\Klass;

use Illuminate\View\Component;
use Illuminate\View\View;
use ReflectionClass;

class ComponentDeclarationFactory
{
    /** @var ComponentDeclaration[] */
    protected static array $declarations = [];

    public static function declarations(): array
    {
        return static::$declarations;
    }

    /** @param class-string $class */
    public static function findOrCreate(string $name, string $class): ComponentDeclaration
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

    public static function find(string $name): ComponentDeclaration
    {
        return static::$declarations[$name];
    }

    public static function add(ComponentDeclaration $declaration): ComponentDeclaration
    {
        static::$declarations[$declaration->getName()] = $declaration;

        return $declaration;
    }

    /** @param class-string $class */
    public static function create(string $name, string $class): ComponentDeclaration
    {
        return new ComponentDeclaration(
            $name,
            $class,
            static::getComponentContents($name, $class),
            []
        );
    }

    /** @param class-string $class */
    protected static function getComponentContents(string $name, string $class): string
    {
        if (str_contains($name, '-')) {
            return static::getComponentContentsWithReflection($class);
        }
        $name = sprintf(
            '%s%s.blade.php',
            resource_path('views/components/'),
            str_replace('.', DIRECTORY_SEPARATOR, $name)
        );

        if (file_exists($name)) {
            return file_get_contents($name) ?: '';
        }

        return static::getComponentContentsWithReflection($class);
    }

    /** @param class-string $class */
    protected static function getComponentContentsWithReflection(string $class): string
    {
        /** @var ReflectionClass<Component> $ref */
        $ref       = new ReflectionClass($class);
        $component = $ref->newInstanceWithoutConstructor()->render();

        // Support for inline components
        if (is_string($component)) {
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
