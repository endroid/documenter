<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Yuml;

class Yuml
{
    private $baseUrl = 'https://yuml.me/diagram/{style}/class/{definitions}';
    private $style = 'plain';
    private $definitions = [];

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

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
        $this->addDefinition('['.$name.']');
    }

    public function addObjects(iterable $names): void
    {
        foreach ($names as $name) {
            $this->addObject($name);
        }
    }

    public function addUses(string $object, string $used): void
    {
        $this->addObjects([$object, $used]);
        $this->addDefinition('['.$object.']uses -.->['.$used.']');
    }

    public function addExtends(string $object, string $parent): void
    {
        $this->addObjects([$object, $parent]);
        $this->addDefinition('['.$parent.']^-['.$object.']');
    }

    public function addImplements(string $object, string $interface): void
    {
        $this->addObjects([$object, $interface]);
        $this->addDefinition('['.$interface.']^-.-implements['.$object.']');
    }

    public function getUrl()
    {
        $replaces = [
            '{style}' => $this->style,
            '{definitions}' => implode(', ', $this->definitions),
        ];

        return str_replace(array_keys($replaces), $replaces, $this->baseUrl);
    }
}
