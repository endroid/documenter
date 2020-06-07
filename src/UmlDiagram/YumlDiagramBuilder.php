<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\UmlDiagram;

use Endroid\Documenter\ClassInfo\ClassInfoFactoryInterface;
use Endroid\Documenter\ClassInfo\ClassInfoInterface;

class YumlDiagramBuilder implements UmlDiagramBuilderInterface
{
    private $classInfoFactory;
    private $paths = [];
    private $whitelist = [];
    private $groups = ['default'];

    public function __construct(ClassInfoFactoryInterface $classInfoFactory)
    {
        $this->classInfoFactory = $classInfoFactory;
    }

    public function addPath(string $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function addWhitelist(string $namespace): self
    {
        $this->whitelist[$namespace] = $namespace;

        return $this;
    }

    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function build(): UmlDiagramInterface
    {
        $umlDiagram = new YumlDiagram();

        foreach ($this->classInfoFactory->getIterator($this->paths, $this->whitelist) as $classInfo) {
            $this->addClassToDiagram($classInfo, $umlDiagram);
        }

        return $umlDiagram;
    }

    private function addClassToDiagram(ClassInfoInterface $classInfo, UmlDiagramInterface $umlDiagram): void
    {
        $className = $classInfo->getName();

        $umlDiagram->addObject($className);
    }
}
