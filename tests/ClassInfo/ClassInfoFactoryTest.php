<?php

declare(strict_types=1);

namespace Endroid\Documenter\Tests\ClassInfo;

use Endroid\Documenter\ClassInfo\ClassInfo;
use Endroid\Documenter\ClassInfo\ClassInfoFactory;
use PHPUnit\Framework\TestCase;

final class ClassInfoFactoryTest extends TestCase
{
    public function testFactoryNeedsNoUnusedAnnotationReader(): void
    {
        $factory = new ClassInfoFactory();
        $info = $factory->createForClass(\RuntimeException::class);

        self::assertSame(\RuntimeException::class, $info->getName());
        self::assertSame(\Exception::class, $info->getExtends());
        self::assertContains(\Throwable::class, $info->getImplements());
        self::assertSame([], $info->getUses());
    }

    public function testLegacyNamedReaderArgumentRemainsAcceptedAndIsNotRetained(): void
    {
        $reader = new \stdClass();
        $reference = \WeakReference::create($reader);
        $info = new ClassInfo(class: \stdClass::class, annotationReader: $reader);
        unset($reader);

        self::assertNull($reference->get());
        self::assertSame(\stdClass::class, $info->getName());
        self::assertNull($info->getExtends());
        self::assertSame([], $info->getImplements());
    }

    public function testCreateLoadsPhpFilesAndReturnsDeclaredClassInformation(): void
    {
        $factory = new ClassInfoFactory();
        $classes = $factory->create([__DIR__ . '/../Fixtures']);

        self::assertArrayHasKey('Endroid\\Documenter\\Tests\\Fixtures\\Subject', $classes);
        self::assertSame(\ArrayObject::class, $classes['Endroid\\Documenter\\Tests\\Fixtures\\Subject']->getExtends());
        self::assertArrayHasKey(\stdClass::class, $classes);
    }
}
