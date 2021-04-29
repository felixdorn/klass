<?php

namespace Tests\Components;

use Illuminate\View\Component;

class VariableDefinedInTheConstructor extends Component
{
    public string $size;

    public function __construct(string $size = '2xl')
    {
        $this->size = $size;
    }

    public function render()
    {
        return view('__klass_tests::variable-defined-in-the-constructor');
    }
}
