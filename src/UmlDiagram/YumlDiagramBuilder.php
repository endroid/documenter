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
    private ClassInfoFactoryInterface $classInfoFactory;
    /** @var list<string> */
    private array $paths = [];
    /** @var array<string, string> */
    private array $whitelist = [];

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

    /** @param array<string> $groups Retained for compatibility; group filtering is not implemented. */
    public function setGroups(array $groups): self
    {
        // Preserve the public named argument, but do not retain unused group state.
        unset($groups);
        return $this;
    }

    #[\Override]
    public function build(): UmlDiagramInterface
    {
        $umlDiagram = new YumlDiagram();

        $classInfos = $this->classInfoFactory->create($this->paths);

        foreach ($classInfos as $classInfo) {
            if (!$this->isWhitelisted($classInfo->getName())) {
                continue;
            }

            $this->addClassToDiagram($classInfo, $umlDiagram);
        }

        return $umlDiagram;
    }

    private function isWhitelisted(string $class): bool
    {
        foreach ($this->whitelist as $namespace) {
            if (str_contains($class, $namespace)) {
                return true;
            }
        }

        return false;
    }

    private function addClassToDiagram(ClassInfoInterface $classInfo, UmlDiagramInterface $umlDiagram): void
    {
        $className = $classInfo->getName();

        $umlDiagram->addObject($className);

        $extends = $classInfo->getExtends();
        if (null !== $extends && $this->isWhitelisted($extends)) {
            $umlDiagram->addExtends($className, $extends);
        }

        $implements = $classInfo->getImplements();
        foreach ($implements as $interface) {
            if (!$this->isWhitelisted($interface)) {
                continue;
            }

            $umlDiagram->addImplements($className, $interface);
        }
    }
}
