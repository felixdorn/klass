<?php

namespace Felix\Klass\Visitors;

use Felix\Klass\Call;

interface Visitor
{
    public function visit(Call $call): void;
}
