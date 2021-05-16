<?php

namespace Felix\Klass;

use Felix\Klass\Visitors\Visitor;

class Calls
{
    /** @var array<int, array<int, string>> */
    protected array $visited = [];
    /** @var Visitor[] */
    protected array $visitors = [];

    /**
     * @param Call[] $calls
     */
    public function __construct(public array $calls = [])
    {
    }

    public function addVisitor(Visitor $visitor): self
    {
        $this->visitors[] = $visitor;

        return $this;
    }

    public function visit(): Calls
    {
        foreach ($this->calls as $call) {
            foreach ($this->visitors as $visitorName => $visitor) {
                if ($this->wasVisited($visitorName, $call)) {
                    continue;
                }
                $this->markAsVisited($visitorName, $call);
                if (is_callable($visitor)) {
                    $visitor($call);
                    continue;
                }

                $visitor->visit($call);
            }
        }

        return $this;
    }

    protected function wasVisited(int $visitor, Call $call): bool
    {
        return in_array($call->getComponent()->getName(), $this->visited[$visitor] ?? [], true);
    }

    protected function markAsVisited(int $visitor, Call $call): Calls
    {
        if (!array_key_exists($visitor, $this->visited)) {
            $this->visited[$visitor] = [];
        }

        $this->visited[$visitor][] = $call->getComponent()->getName();

        return $this;
    }

    public function length(): int
    {
        return count($this->calls);
    }
}
