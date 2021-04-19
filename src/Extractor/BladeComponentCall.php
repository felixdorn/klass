<?php

namespace Felix\TailwindClassExtractor\Extractor;

class BladeComponentCall
{
    protected string $name;
    protected array $attributes;
    protected string $class;

    public function __construct(string $name, string $class, array $attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->class = $class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
