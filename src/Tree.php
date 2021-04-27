<?php

namespace Felix\Klass;

class Tree
{
    /** @var ComponentCall[] */
    protected array $tree;
    /** @var string[] */
    protected array $visited = [];

    public function __construct(array $tree = [])
    {
        $this->tree = $tree;
    }

    public function visit(callable $visitor): self
    {
        foreach ($this->tree as $node) {
            if ($this->wasVisited($node)) {
                continue;
            }

            $visitor($node);
            $this->markAsVisited($node);
        }

        return $this;
    }

    protected function wasVisited(ComponentCall $node): bool
    {
        return in_array($node->getComponent()->getName(), $this->visited);
    }

    protected function markAsVisited(ComponentCall $node): Tree
    {
        $this->visited[] = $node->getComponent()->getName();

        return $this;
    }
}
