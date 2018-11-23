<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Yuml;

use Endroid\Documenter\ClassInfo;
use Endroid\Documenter\Whitelist;

class YumlBuilder
{
    private $loadPaths = [];
    private $whitelist;
    private $addRelatedClasses = false;

    private $availableClasses;

    public static function create(): self
    {
        return new self();
    }

    public function setLoadPaths(iterable $loadPaths): self
    {
        $this->loadPaths = $loadPaths;

        return $this;
    }

    public function setWhitelist(Whitelist $whitelist): self
    {
        $this->whitelist = $whitelist;

        return $this;
    }

    public function setAddRelatedClasses(bool $addRelatedClasses): self
    {
        $this->addRelatedClasses = $addRelatedClasses;

        return $this;
    }

    public function build(): Yuml
    {
        $yuml = new Yuml();

        $this->loadPaths();

        $yumlClasses = $this->getYumlClassIterator();
        foreach ($yumlClasses as $yumlClass) {
            try {
                $classInfo = new ClassInfo($yumlClass);
                $this->addToYuml($classInfo, $yuml);
            } catch (\ReflectionException $exception) {
                // do nothing
            }
        }

        return $yuml;
    }

    private function loadPaths(): void
    {
        foreach ($this->loadPaths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if ($item->isFile() && 'php' === $item->getExtension()) {
                    include_once $item->getPathname();
                }
            }
        }
    }

    private function getYumlClassIterator(): iterable
    {
        $this->availableClasses = get_declared_classes();

        while (count($this->availableClasses) > 0) {
            $class = array_shift($this->availableClasses);
            if ($this->whitelist->isWhitelisted($class)) {
                yield $class;
            }
        }
    }

    private function addToYuml(ClassInfo $classInfo, Yuml $yuml): void
    {
        $className = $classInfo->getName();

        $yuml->addObject($className);

        $uses = $classInfo->getUses();
        $extends = $classInfo->getParentClass();
        $implements = $classInfo->getInterfaceNames();

        // Extends
        if ($extends) {
            $this->handleRelatedClass($parentClassName = $classInfo->getParentClass()->getName());
            if ($this->whitelist->isWhitelisted($parentClassName = $classInfo->getParentClass()->getName())) {
                $yuml->addExtends($className, $parentClassName);
            }
        }

        // Implements
        foreach ($implements as $interfaceName) {
            $this->handleRelatedClass($interfaceName);
            if ($this->whitelist->isWhitelisted($interfaceName) && in_array($interfaceName, $uses)) {
                $yuml->addImplements($className, $interfaceName);
            }
        }

        // Uses
//        foreach ($uses as $useClass) {
//            $this->handleRelatedClass($useClass);
//            if ($this->whitelist->isWhitelisted($useClass)) {
//                $yuml->addUses($className, $useClass);
//            }
//        }
    }

    private function handleRelatedClass(string $class): void
    {
        if (!$this->addRelatedClasses) {
            return;
        }

        $this->availableClasses[] = $class;
        $this->whitelist->prependRule($class, Whitelist::INCLUDE);
    }
}
