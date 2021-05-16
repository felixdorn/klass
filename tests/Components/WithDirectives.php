<?php

namespace Tests\Components;

use Illuminate\View\Component;

class WithDirectives extends Component
{
    public function render()
    {
        return view('__klass_tests::with-directives');
    }
}
