<?php

namespace Felix\Klass\Facades;

use Felix\Klass\Klass as Concrete;
use Felix\Klass\Tree;

/**
 * @method static Tree tree()
 */
class Klass
{
    /** @return mixed */
    public static function __callStatic(string $name, array $arguments)
    {
        /* @phpstan-ignore-next-line */
        return call_user_func_array([Concrete::getInstance(), $name], $arguments);
    }
}
