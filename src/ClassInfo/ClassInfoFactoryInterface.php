<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\ClassInfo;

interface ClassInfoFactoryInterface
{
    /**
     * @param array<string> $paths
     * @return array<class-string, ClassInfoInterface>
     */
    public function create(array $paths): array;

    /** @param class-string $class */
    public function createForClass(string $class): ClassInfoInterface;
}
