<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter;

class Whitelist
{
    public const ANALIZE = 'analyze';
    public const INCLUDE = 'include';
    public const IGNORE = 'ignore';

    private $rules = [];

    public function __construct(iterable $rules)
    {
        foreach ($rules as $class => $rule) {
            $this->appendRule($class, $rule);
        }
    }

    public function appendRule(string $class, string $rule): self
    {
        $this->rules = $this->rules + [$class => $rule];

        return $this;
    }

    public function prependRule(string $class, string $rule): self
    {
        $this->rules = [$class => $rule] + $this->rules;

        return $this;
    }

    public function isWhiteListed(string $string): bool
    {
        $isWhitelisted = false;
        foreach ($this->rules as $pattern => $whitelist) {
            if (preg_match('#^'.preg_quote($pattern, '#').'#', $string)) {
                $isWhitelisted = $whitelist;
            }
        }

        return $isWhitelisted;
    }
}
