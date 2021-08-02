<?php

declare(strict_types=1);

namespace ArcticButterfly;

final class DependencyDefinition
{
    /*
     * @param string $className
     * @param string[] $dependencies
     */
    public function __construct(private string $className, private array $dependencies)
    {
    }

    public function dependsOn(string $className): bool
    {
        return in_array($className, $this->dependencies);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function __toString()
    {
        return $this->className;
    }
}
