<?php

namespace Tests\Components;

use Illuminate\View\Component;

class VariableDefinedInPublicProperty extends Component
{
    public string $rounded = 'full';

    public function __construct(string $rounded)
    {
        $this->rounded = $rounded;
    }

    public function render()
    {
        return view('__klass_tests::variable-defined-in-public-property');
    }
}
