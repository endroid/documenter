<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter;

class ClassInfo extends \ReflectionClass
{
    public function getUses(bool $filtered = true): iterable
    {
        $uses = [];

        if (!$this->isUserDefined()) {
            return [];
        }

        $tokens = token_get_all(file_get_contents($this->getFileName()));

        $record = false;
        $currentUse = '';
        foreach ($tokens as $token) {
            if ($record) {
                if (';' === $token || T_AS === $token[0]) {
                    $uses[] = trim($currentUse);
                    $currentUse = '';
                    $record = false;
                } elseif (is_string($token)) {
                    $currentUse .= $token;
                } else {
                    $currentUse .= $token[1];
                }
            } elseif (T_USE === $token[0]) {
                $record = true;
                continue;
            }
        }

        if ($filtered) {
            $uses = array_filter($uses, [$this, 'filter']);
        }

        return $uses;
    }

    private function filter(string $class): bool
    {
        if ($this->getParentClass() && $class === $this->getParentClass()->getName()) {
            return false;
        }

        foreach ($this->getInterfaceNames() as $interfaceName) {
            if ($class === $interfaceName) {
                return false;
            }
        }

        return true;
    }
}
