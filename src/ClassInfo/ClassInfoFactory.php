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

class ClassInfoFactory implements ClassInfoFactoryInterface
{
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }

    public function getIterator(array $paths, array $whitelist): \Generator
    {
        // First make sure all paths are loaded
        foreach ($paths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if ($item->isFile() && 'php' === $item->getExtension()) {
                    include_once $item->getPathname();
                }
            }
        }

        $availableClasses = get_declared_classes();
        foreach ($availableClasses as $class) {
            foreach ($whitelist as $namespace) {
                if (false !== strpos($class, $namespace)) {
                    yield $this->createForClass($class);
                }
            }
        }
    }

    public function createForClass(string $class): ClassInfoInterface
    {
        return new ClassInfo($class, $this->annotationReader);
    }
}
