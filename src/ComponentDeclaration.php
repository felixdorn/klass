<?php

namespace Felix\Klass;

class ComponentDeclaration
{
    protected string $name;
    protected string $class;
    protected array $attributes;
    protected string $contents;

    public function __construct(string $name, string $class, string $contents, array $attributes = [])
    {
        $this->name       = $name;
        $this->class      = $class;
        $this->attributes = $attributes;
        $this->contents   = $contents;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array<string, scalar>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
