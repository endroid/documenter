<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\ClassInfo;

interface ClassInfoInterface
{
    public function getName(): string;

    public function getExtends(): ?string;

    /** @return list<class-string> */
    public function getImplements(): array;

    /** @return list<class-string> */
    public function getUses(): array;
}
