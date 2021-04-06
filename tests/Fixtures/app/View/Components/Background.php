<?php

namespace Tests\Fixtures\app\View\Components;

use Illuminate\View\Component;

class Background extends Component
{
    public string $color;

    public function __construct(string $color = 'blue')
    {
        $this->color = $color;
    }

    public function render()
    {
        return view('tests::background');
    }
}
