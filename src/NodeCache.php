<?php

declare(strict_types=1);

namespace PhpStag;

use PhpDs\{
    Node,
    Set
};
use InvalidArgumentException;

final class NodeCache
{
    /**
     * Use the same instances
     * so that when dependencies
     * are added to a dependency,
     * the change will be made
     * in the parent node.
     *
     * @var array<string, Node> $nodeCache
     */
    private array $nodeCache = [];

    public function __construct(private Set $instances)
    {
    }

    public function getNode(string $className): Node
    {
        if (key_exists($className, $this->nodeCache)) {
            return $this->nodeCache[$className];
        }
        $node = $this->isAnInstance($className)
            ? new InstanceNode($className, $this->instances->get($className))
            : new ClassNode($className);
        $this->nodeCache[$className] = $node;
        return $node;
    }

    private function isAnInstance(string $className): bool
    {
        return $this->instances->has($className);
    }

    public function getDependant(string $className): ClassNode
    {
        if ($this->isAnInstance($className)) {
            $message =
              "${className} is an instance." . PHP_EOL;
            $message .= "Did you configure this as an instance and set a dependency definition for it's class?";
            throw new InvalidArgumentException($message);
        }
        $node = new ClassNode($className);
        $this->nodeCache[$className] = $node;
        return $node;
    }
}
