<?php

namespace Felix\Klass;

class Call
{
    protected Declaration $component;
    protected array $attributes;

    public function __construct(Declaration $component, array $attributes = [])
    {
        $this->component  = $component;
        $this->attributes = $attributes;
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
