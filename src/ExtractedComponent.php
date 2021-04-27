<?php

namespace Felix\Klass;

class ExtractedComponent
{
    protected ComponentDeclaration $declaration;
    protected array $calls;

    public function __construct(ComponentDeclaration $declaration, array $calls)
    {
        $this->declaration = $declaration;
        $this->calls       = $calls;
    }

    public function hasCalls(): bool
    {
        return !empty($this->calls);
    }

    /**
     * @return ComponentCall[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getDeclaration(): ComponentDeclaration
    {
        return $this->declaration;
    }
}
