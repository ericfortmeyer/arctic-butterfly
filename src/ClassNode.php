<?php

declare(strict_types=1);

namespace ArcticButterfly;

use Countable;
use PhpDs\{
    Node,
    Tree
};

final class ClassNode extends Tree implements Countable
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function getValue(): object
    {
        $className = $this->getClassName();
        return new $className(...$this->children->map(fn (Node $child) => $child->getValue()));
    }

    private function getClassName(): string
    {
        return (string) $this->value;
    }

    public function addArgument(Node $node): void
    {
        $this->addChild($node);
    }

    public function count(): int
    {
        return array_sum(($this->children->map(fn ($item) => count($item, COUNT_RECURSIVE)))) + 1;
    }

    public function __toString()
    {
        return $this->getClassName();
    }
}
