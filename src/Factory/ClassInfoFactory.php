<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use Endroid\Documenter\ClassInfo;

class ClassInfoFactory
{
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function createForClass(string $class): ClassInfo
    {
        $classInfo = new ClassInfo($class);
    }
}
