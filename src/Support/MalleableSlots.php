<?php

namespace Felix\Klass\Support;

use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;
use IteratorAggregate;

class MalleableSlots implements Arrayable, Countable, IteratorAggregate
{
    public array $slots = [];

    public function __get(string $name): HtmlString
    {
        if (array_key_exists($name, $this->slots)) {
            return $this->slots[$name];
        }

        return new HtmlString();
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->slots[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return true;
    }

    public function __unset(string $name): void
    {
        unset($this->slots[$name]);
    }

    public function toArray(): array
    {
        return $this->slots;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->slots);
    }

    public function count(): int
    {
        // + 1 because as it includes the default slots
        return count($this->slots) + 1;
    }
}
