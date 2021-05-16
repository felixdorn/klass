<?php

namespace Felix\Klass;

class Declaration
{
    public function __construct(
        protected string $name,
        protected string $class,
        protected string $contents,
        protected array $attributes = []
    ) {
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
