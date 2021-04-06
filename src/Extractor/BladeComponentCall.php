<?php

namespace Felix\TailwindClassExtractor\Extractor;

class BladeComponentCall
{
    protected string $name;
    protected array $attributes;

    public function __construct(string $name, array $attributes)
    {
        $this->name       = $name;
        $this->attributes = $attributes;
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
