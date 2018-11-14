<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Yuml;

use Endroid\Documenter\Whitelist;

class YumlBuilder
{
    public const MODE_IGNORE = 'ignore';
    public const MODE_ANNOTATION = 'annotation';

    private $loadPaths = [];
    private $mode = self::MODE_IGNORE;
    private $whitelist;

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

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function build(): Yuml
    {
        $yuml = new Yuml();

        $this->loadPaths();
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if ($this->whitelist->isWhiteListed($class)) {
                $this->analyzeClass($class);
            }
        }

        return $yuml;
    }

    private function loadPaths(): void
    {
        foreach ($this->loadPaths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if ($item->isFile() && $item->getExtension() === 'php') {
                    include_once($item->getPathname());
                }
            }
        }
    }
}
