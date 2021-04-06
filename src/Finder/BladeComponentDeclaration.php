<?php

namespace Felix\TailwindClassExtractor\Finder;

class BladeComponentDeclaration
{
    protected string $name;
    protected array $attributes;
    protected array $defaults;

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function __construct(string $name, array $attributes, array $defaults = [])
    {
        $this->name       = $name;
        $this->attributes = $attributes;
        $this->defaults   = $defaults;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
