<?php

namespace Felix\Klass\Visitors;

use Felix\Klass\Call;

class CallableVisitor implements Visitor
{
    /**
     * @param callable $visitor
     */
    public function __construct(public $visitor)
    {
    }

    public function visit(Call $call): void
    {
        call_user_func($this->visitor, $call);
    }
}
