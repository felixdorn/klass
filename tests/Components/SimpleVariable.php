<?php

namespace Tests\Components;

use Illuminate\View\Component;

class SimpleVariable extends Component
{
    public string $color;

    public function __construct(string $color)
    {
        $this->color = $color;
    }

    public function render()
    {
        return view('__klass_tests::simple-variable');
    }
}
