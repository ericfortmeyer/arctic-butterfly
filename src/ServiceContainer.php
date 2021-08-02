<?php

declare(strict_types=1);

namespace PhpAnt;

final class ServiceContainer implements \Psr\Container\ContainerInterface
{
    private ?DependencyGraph $dependencyGraph = null;

    public function __construct(ServiceContainerConfig $config)
    {
        $config->configure($this->dependencyGraph);
    }

     /**
      * Create and inject all instances
      * without using Reflection
      * Return the root from the dependency graph
      * No need to get services from the container
      */
    public function initializeRoot(): object
    {
        /**
         * Each node in the dependency graph
         * is designed to provide an instance
         * of itself with all constructor
         * arguments added.
         */

        $dependencyRoot = $this->dependencyGraph->getRoot();
        $appInstance = $dependencyRoot->getValue();

        return $appInstance;
    }

    public function get($id)
    {
        return (object)"";
    }

    public function has($id)
    {
        return false;
    }
}
