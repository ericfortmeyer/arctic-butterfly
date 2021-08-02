<?php

namespace PhpAnt;

use Countable;
use PhpDs\Node;

final class InstanceNode extends Node implements Countable
{
    public function __construct(private string $className, object $instance)
    {
        parent::__construct($instance);
    }

    public function getValue(): object
    {
        return $this->value;
    }

    private function getClassName(): string
    {
        return $this->className;
    }

    public function count(): int
    {
        return 0;
    }

    public function __toString()
    {
        return $this->getClassName();
    }
}
