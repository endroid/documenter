<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\ClassInfo;

use ReflectionClass;

class ClassInfo implements ClassInfoInterface
{
    /** @var ReflectionClass<object> */
    private ReflectionClass $reflectionClass;

    /**
     * @param class-string $class
     * @param object|null $annotationReader Retained for compatibility; annotations are not used.
     */
    public function __construct(string $class, ?object $annotationReader = null)
    {
        // Explicitly discard the legacy argument without retaining an unused reader.
        unset($annotationReader);
        $this->reflectionClass = new ReflectionClass($class);
    }

    #[\Override]
    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    #[\Override]
    public function getExtends(): ?string
    {
        $parentClass = $this->reflectionClass->getParentClass();
        if (false === $parentClass) {
            return null;
        }

        return $parentClass->getName();
    }

    /** @return list<class-string> */
    #[\Override]
    public function getImplements(): array
    {
        return $this->reflectionClass->getInterfaceNames();
    }

    /** @return list<class-string> */
    #[\Override]
    public function getUses(): array
    {
        return [];
    }
}
