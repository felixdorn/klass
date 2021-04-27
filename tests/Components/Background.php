<?php

namespace Tests\Components;

use Illuminate\View\Component;

class Background extends Component
{
    public string $color;

    public function __construct(string $color = 'indigo')
    {
        $this->color = $color;
    }

    public function render()
    {
        return <<<'BLADE'
<div class="bg-{{ $color }}-500">{{ $slot }}</div>
BLADE;
    }
}
