<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Yuml;

class YumlBuilder
{
    public const MODE_WHITELIST = 'whitelist';
    public const MODE_BLACKLIST = 'blacklist';

    private $paths = [];
    private $mode = self::MODE_BLACKLIST; // show all when no mode is set
    private $whitelist = [];
    private $blacklist = [];
    private $includeExternal = false;

    public static function create(): self
    {
        return new self();
    }

    public function setPath(string $path): self
    {
        $this->paths = [$path];
    }

    public function setPaths(iterable $paths): self
    {
        $this->paths = $paths;
    }

    public function setWhitelist(iterable $whitelist, bool $toggleMode = true): self
    {
        $this->whitelist = $whitelist;

        if ($toggleMode) {
            $this->mode = self::MODE_WHITELIST;
        }

        return $this;
    }

    public function setBlacklist(iterable $blacklist, bool $toggleMode = true): self
    {
        $this->blacklist = $blacklist;

        if ($toggleMode) {
            $this->mode = self::MODE_BLACKLIST;
        }

        return $this;
    }

    public function setIncludeExternal(bool $includeExternal): self
    {
        $this->includeExternal = $includeExternal;

        return $this;
    }

    public function build(): Yuml
    {
        $yuml = new Yuml();

        $classMap = $this->getClassMap($this->paths, $this->excludes);



        return $yuml;
    }

    private function getClassMap(iterable $include, iterable $exclude)
    {
        /** @var \SplFileInfo[] $iterator */
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getExtension() === 'php') {
                dump($item);
                die;
                require_once($item->getPathname());
            }
        }
    }
}
