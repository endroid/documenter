<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\ClassInfo;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;

class ClassInfo implements ClassInfoInterface
{
    private $reflectionClass;
    private $annotationReader;

    /* @param class-string $class */
    public function __construct(string $class, AnnotationReader $annotationReader)
    {
        $this->reflectionClass = new ReflectionClass($class);
        $this->annotationReader = $annotationReader;
    }

    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getExtends(): ?string
    {
        if (false === $this->reflectionClass->getParentClass()) {
            return null;
        }

        return $this->reflectionClass->getParentClass()->getName();
    }

    public function getImplements(): array
    {
        return $this->reflectionClass->getInterfaceNames();
    }

    public function getUses(): array
    {
        return [];
    }
}
