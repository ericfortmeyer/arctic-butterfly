<?php

namespace PhpStag;

use PhpDs\Node;

final class ScalarNode extends Node
{
    public function __construct(string | bool | int | float | null $value)
    {
        parent::__construct($value);
    }

    public function getValue(): string | bool | int | float | null
    {
        return $this->value;
    }
}
