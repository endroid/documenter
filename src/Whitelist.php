<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter;

class Whitelist
{
    private $directives = [];

    public function __construct(iterable $directives)
    {
        foreach ($directives as $directive) {
            if (substr($directive, 0, 1) === '!') {
                $this->directives[substr($directive, 1)] = false;
            } else {
                $this->directives[$directive] = true;
            }
        }
    }

    public function isWhiteListed(string $string): bool
    {
        $isWhitelisted = false;
        foreach ($this->directives as $pattern => $whitelist) {
            if (preg_match('#^'.preg_quote($pattern, '#').'#', $string)) {
                $isWhitelisted = $whitelist;
            }
        }

        return $isWhitelisted;
    }
}
