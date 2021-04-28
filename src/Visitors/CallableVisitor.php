<?php

namespace Felix\Klass\Visitors;

use Felix\Klass\Call;

class CallableVisitor implements Visitor
{
    /** @var callable */
    protected $visitor;

    public function __construct(callable $visitor)
    {
        $this->visitor = $visitor;
    }

    public function visit(Call $call): void
    {
        call_user_func($this->visitor, $call);
    }
}
