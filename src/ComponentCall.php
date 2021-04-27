<?php

namespace Felix\Klass;

class ComponentCall
{
    protected ComponentDeclaration $component;
    protected array $attributes;

    public function __construct(ComponentDeclaration $component, array $attributes = [])
    {
        $this->component  = $component;
        $this->attributes = $attributes;
    }

    public function getComponent(): ComponentDeclaration
    {
        return $this->component;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
