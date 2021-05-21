<?php

namespace Felix\Klass\Facades;

use Felix\Klass\Calls;
use Felix\Klass\Klass as Concrete;

/**
 * @method static Calls calls()
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
