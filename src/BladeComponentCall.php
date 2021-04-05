<?php


namespace Felix\TailwindClassExtractor;


use Illuminate\View\ComponentAttributeBag;

class BladeComponentCall
{
    protected string $name;
    protected ComponentAttributeBag $attributes;

    public function __construct(string $name, ComponentAttributeBag $attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): ComponentAttributeBag
    {
        return $this->attributes;
    }
}
