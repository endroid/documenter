<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Yuml;

use Doctrine\Common\Annotations\AnnotationReader;
use Endroid\Documenter\Annotation\Documenter;
use Endroid\Documenter\ClassInfo;
use Endroid\Documenter\Factory\ClassInfoFactory;
use Endroid\Documenter\Whitelist;
use ReflectionClass;

class YumlBuilder
{
    private $classInfoFactory;
    private $paths;
    private $groups;
    private $classInfo;

    public function __construct(ClassInfoFactory $classInfoFactory)
    {
        $this->classInfoFactory = $classInfoFactory;
        $this->paths = [];
        $this->groups = [];
        $this->classInfo = [];
    }

    public function addPath(string $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function build(): Yuml
    {
        $yuml = new Yuml();

        $this->loadClassInfo();

        $filteredClassInfo = $this->filterClassInfo($this->groups);

        foreach ($annotations as $class => $annotation) {
            $reflectionClass = new ClassInfo($class);
            $this->addToYuml($reflectionClass, $yuml);
        }

        return $yuml;
    }

    private function loadClassInfo(): void
    {
        // First make sure all paths are loaded
        foreach ($this->paths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if ($item->isFile() && 'php' === $item->getExtension()) {
                    include_once $item->getPathname();
                }
            }
        }

        $availableClasses = get_declared_classes();
        foreach ($availableClasses as $class) {
            $this->classInfo[$class] = $this->classInfoFactory->createForClass($class);
        }

        dump($this->classInfo);
        die;
    }

    private function filterClassInfo(array $groups): array
    {
        return $this->classInfo;
    }

    private function addToYuml(ClassInfo $classInfo, Yuml $yuml): void
    {
        $className = $classInfo->getName();

        $yuml->addObject($className);

        $uses = $classInfo->getUses();
        $extends = $classInfo->getParentClass();
        $implements = $classInfo->getInterfaceNames();

        // Extends
        if ($extends instanceof ReflectionClass) {
            if ($this->whitelist->isWhitelisted($parentClassName = $extends->getName())) {
                $yuml->addExtends($className, $parentClassName);
            }
        }

        // Implements
        foreach ($implements as $interfaceName) {
            if ($this->whitelist->isWhitelisted($interfaceName) && in_array($interfaceName, $uses)) {
                $yuml->addImplements($className, $interfaceName);
            }
        }

        // Uses
        foreach ($uses as $useClass) {
            $this->handleRelatedClass($useClass);
            if ($this->whitelist->isWhitelisted($useClass)) {
                $yuml->addUses($className, $useClass);
            }
        }
    }
}
