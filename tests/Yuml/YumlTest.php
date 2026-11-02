<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Documenter\Tests\Yuml;

use Endroid\Documenter\ClassInfo\ClassInfoFactoryInterface;
use Endroid\Documenter\ClassInfo\ClassInfoInterface;
use Endroid\Documenter\UmlDiagram\YumlDiagram;
use Endroid\Documenter\UmlDiagram\YumlDiagramBuilder;
use PHPUnit\Framework\TestCase;

final class YumlTest extends TestCase
{
    public function testDiagramEscapesNamesAndDeduplicatesDefinitions(): void
    {
        $diagram = new YumlDiagram();
        $diagram->setStyle('scruffy');
        $diagram->addObject('App\\Subject');
        $diagram->addExtends('App\\Subject', 'App\\Parent');
        $diagram->addImplements('App\\Subject', 'App\\Contract');
        $diagram->addUses('App\\Subject', 'App\\Dependency');

        self::assertSame(
            'https://yuml.me/diagram/scruffy/class/[App/Subject], [App/Parent], [App/Parent]^-['
            . 'App/Subject], [App/Contract], [App/Contract]^-.-implements[App/Subject], '
            . '[App/Dependency], [App/Subject]uses -.->[App/Dependency]',
            $diagram->getUrl(),
        );
    }

    public function testBuilderPreservesSubstringWhitelistAndLegacyGroupsPlaceholder(): void
    {
        $info = $this->createStub(ClassInfoInterface::class);
        $info->method('getName')->willReturn('Prefix\\App\\Subject');
        $info->method('getExtends')->willReturn('App\\Parent');
        $info->method('getImplements')->willReturn(['App\\Contract', 'Other\\Contract']);
        $excluded = $this->createStub(ClassInfoInterface::class);
        $excluded->method('getName')->willReturn('Other\\Subject');
        $factory = $this->createMock(ClassInfoFactoryInterface::class);
        $factory->expects(self::exactly(2))->method('create')->with(['source'])->willReturn([$info, $excluded]);
        $builder = new YumlDiagramBuilder($factory);

        self::assertSame($builder, $builder->addPath('source'));
        self::assertSame($builder, $builder->addWhitelist('App\\'));
        $url = $builder->build()->getUrl();
        self::assertSame($builder, $builder->setGroups(groups: ['different']));
        self::assertSame($url, $builder->build()->getUrl());
        self::assertStringContainsString('[Prefix/App/Subject]', $url);
        self::assertStringContainsString('[App/Parent]^-[Prefix/App/Subject]', $url);
        self::assertStringContainsString('[App/Contract]^-.-implements[Prefix/App/Subject]', $url);
        self::assertStringNotContainsString('Other', $url);
    }
}
