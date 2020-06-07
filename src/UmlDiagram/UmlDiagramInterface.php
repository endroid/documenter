<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\UmlDiagram;

interface UmlDiagramInterface
{
    public function addObject(string $name): void;
    public function addExtends(string $object, string $parent): void;
    public function addImplements(string $object, string $interface): void;
    public function getUrl(): string;
}
