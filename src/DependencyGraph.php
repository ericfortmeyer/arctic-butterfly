<?php

declare(strict_types=1);

namespace PhpAnt;

use PhpDs\{
    Set,
    Tree
};

final class DependencyGraph
{
    private Tree $graph;

    public function __construct(Set $instances, Set $dependencyDefinitions)
    {
        $nodeCache = new NodeCache($instances);
        /*
          O(n)
          Offset loop
          Create a node and add dependancies
          as you go.
          Since everything is a ref,
          dependencies added to a dependency
          will automatically be added to it's
          dependant. This avoids extra work.
          A max tracker can be used to
          determine which node contains all
          resloved dependencies.
        */

        $createDefinitions =
            fn (int $i, string $className) => new DependencyDefinition($className, []);

        $listOfDependants = new Set();
        $instanceClassNames = $instances->keys();
        $instanceDefinitions =
            array_map($createDefinitions, range(0, count($instanceClassNames) - 1), $instanceClassNames);
        $definitionsAndInstances = array_merge($instanceDefinitions, $dependencyDefinitions->toArray());
        $numberOfDependencies = count($definitionsAndInstances);

        // will not go to the last element because it will have
        // already been checked
        for ($i = 0; $i < $numberOfDependencies - 1; ++$i) {
            $indexOfSibling = $i + 1;
            $isNextToLastItem = $indexOfSibling === $numberOfDependencies - 1;
            $current = $definitionsAndInstances[$i];
            $isAnInstance = $instances->has($current->getClassName());
            $classNameOfCurrent = $current->getClassName();

            if ($isNextToLastItem) {
                $lastItem = $definitionsAndInstances[$indexOfSibling];
                $classNameOfLastItem = $lastItem->getClassName();
                $lastNode = $nodeCache->getNode($classNameOfLastItem);
                $listOfDependants->add($classNameOfLastItem, $lastNode);
            }

            if ($isAnInstance) {
                $instanceNode = $nodeCache->getNode($classNameOfCurrent);
                $listOfDependants->add($classNameOfCurrent, $instanceNode);
                // only check if the instance is a dependency
                for (; $indexOfSibling < $numberOfDependencies; ++$indexOfSibling) {
                    $sibling = $definitionsAndInstances[$indexOfSibling];
                    $classNameOfSibling = $sibling->getClassName();
                    if ($sibling->dependsOn($classNameOfCurrent)) {
                        $dependant = $nodeCache->getDependant($classNameOfSibling);
                        $dependency = $nodeCache->getNode($classNameOfCurrent);
                        $dependant->addArgument($dependency);
                        $listOfDependants->add($classNameOfCurrent, $dependency);
                    }
                }
            } else {
                for (; $indexOfSibling < $numberOfDependencies; ++$indexOfSibling) {
                    // does a dependency exist with current and sibling
                    $sibling = $definitionsAndInstances[$indexOfSibling];
                    $classNameOfSibling = $sibling->getClassName();
                    if ($current->dependsOn($classNameOfSibling)) {
                        $dependant = $nodeCache->getDependant($classNameOfCurrent);
                        $dependency = $nodeCache->getNode($classNameOfSibling);
                        $dependant->addArgument($dependency);
                        $listOfDependants->add($classNameOfCurrent, $dependant);
                    } elseif ($sibling->dependsOn($classNameOfCurrent)) {
                        $dependant = $nodeCache->getDependant($classNameOfSibling);
                        $dependency = $nodeCache->getNode($classNameOfCurrent);
                        $dependant->addArgument($dependency);
                        $listOfDependants->add($classNameOfCurrent, $dependency);
                    }
                }
            }
        }

        /**
         * Recursively count all dependencies.
         * The dependant with the highest number of dependencies
         * is the dependency graph.
         */
        $this->graph = $listOfDependants->max();
    }

    public function getRoot(): Tree
    {
        return $this->graph;
    }
}
