<?php

namespace Tests\Components;

use Illuminate\View\Component;

class VariableInString extends Component
{
    public string $width;

    public function __construct(string $width)
    {
        $this->width = $width;
    }

    public function render()
    {
        return view('__klass_tests::variable-in-string');
    }
}
