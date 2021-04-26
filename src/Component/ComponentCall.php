<?php

namespace Felix\Klass\Component;

class ComponentCall
{
    protected string $name;
    protected array $attributes;
    /** @var class-string */
    protected string $class;

    /**
     * @param class-string $class
     */
    public function __construct(string $name, string $class, array $attributes)
    {
        $this->name       = $name;
        $this->attributes = $attributes;
        $this->class      = $class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
