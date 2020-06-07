<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\ClassInfo;

use Doctrine\Common\Annotations\AnnotationReader;
use Endroid\Documenter\Exception\FileLoadException;
use ReflectionClass;

class ClassInfo implements ClassInfoInterface
{
    private $reflectionClass;
    private $annotationReader;

    public function __construct(string $class, AnnotationReader $annotationReader)
    {
        $this->reflectionClass = new ReflectionClass($class);
        $this->annotationReader = $annotationReader;
    }

    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getExtends(): ?string
    {
        dump(get_class($this->reflectionClass->getParentClass()));
        die;

        return $this->reflectionClass->getParentClass()->getName();
    }

    public function getImplements(): array
    {
        return $this->reflectionClass->getInterfaceNames();
    }

    public function getUses(): array
    {
        return [];
    }

    public function getUsssses(bool $filtered = true): array
    {
        $uses = [];

        if (!$this->isUserDefined()) {
            return [];
        }

        $contents = file_get_contents((string) $this->getFileName());

        if (!is_string($contents)) {
            throw new FileLoadException('Could not load file '.$this->getFileName());
        }

        $tokens = token_get_all($contents);

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
        $parentClass = $this->getParentClass();

        if ($parentClass instanceof ReflectionClass && $class === $parentClass->getName()) {
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