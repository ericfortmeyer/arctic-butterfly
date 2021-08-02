<?php

declare(strict_types=1);

namespace PhpAnt;

use PhpDs\Set;
use Closure;

final class ServiceContainerConfig
{
    private Set $instances;

    private Set $dependencyDefinitions;

    public function __construct()
    {
        $this->instances = new Set();
        $this->dependencyDefinitions = new Set();
    }

    public function addSingleton(string $className, Closure $provider): void
    {
        $instance = $provider();
        $this->instances->add($className, $instance);
    }

    public function addDependencyDefinition(string $dependant, string $dependency): void
    {
        $def = new DependencyDefinition($dependant, [$dependency]);
        $this->dependencyDefinitions->add($dependant, $def);
    }

    public function addDependencyDefinitions(string $dependant, array $dependencies): void
    {
        $def = new DependencyDefinition($dependant, $dependencies);
        $this->dependencyDefinitions->add($dependant, $def);
    }

    public function configure(?DependencyGraph &$graph): void
    {
        /* Set the container's graph */
        $graph = new DependencyGraph(instances: $this->instances, dependencyDefinitions: $this->dependencyDefinitions);
    }
}
