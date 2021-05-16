<?php

namespace Felix\Klass;

class Call
{
    public function __construct(protected Declaration $component, protected array $attributes = [])
    {
    }

    public function getAttributes(): array
    {
        return array_merge($this->getComponent()->getAttributes(), $this->attributes);
    }

    public function getComponent(): Declaration
    {
        return $this->component;
    }
}
