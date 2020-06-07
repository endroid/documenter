<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\UmlDiagram;

class YumlDiagram implements UmlDiagramInterface
{
    private $baseUrl = 'https://yuml.me/diagram/{style}/class/{definitions}';
    private $style = 'plain';
    private $definitions = [];

    public function setStyle(string $style): void
    {
        $this->style = $style;
    }

    private function addDefinition(string $definition): void
    {
        $this->definitions[$definition] = $definition;
    }

    public function addObject(string $name): void
    {
        $this->addDefinition('['.$this->escapeName($name).']');
    }

    public function addExtends(string $object, string $parent): void
    {
        $this->addObject($object);
        $this->addObject($parent);
        $this->addDefinition('['.$this->escapeName($parent).']^-['.$this->escapeName($object).']');
    }

    public function addImplements(string $object, string $interface): void
    {
        $this->addObject($object);
        $this->addObject($interface);
        $this->addDefinition('['.$this->escapeName($interface).']^-.-implements['.$this->escapeName($object).']');
    }

    public function addUses(string $object, string $used): void
    {
        $this->addObject($object);
        $this->addObject($used);
        $this->addDefinition('['.$this->escapeName($object).']uses -.->['.$this->escapeName($used).']');
    }

    public function escapeName(string $name): string
    {
        return str_replace('\\', '/', $name);
    }

    public function getUrl(): string
    {
        $replaces = [
            '{style}' => $this->style,
            '{definitions}' => implode(', ', $this->definitions),
        ];

        $url = str_replace(array_keys($replaces), $replaces, $this->baseUrl);

        return $url;
    }
}
