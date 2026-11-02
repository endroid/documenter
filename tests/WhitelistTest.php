<?php

declare(strict_types=1);

namespace Endroid\Documenter\Tests;

use Endroid\Documenter\Whitelist;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WhitelistTest extends TestCase
{
    /** @return iterable<string, array{string, bool}> */
    public static function rules(): iterable
    {
        yield 'include' => [Whitelist::INCLUDE, true];
        yield 'analyze' => [Whitelist::ANALIZE, true];
        yield 'ignore' => [Whitelist::IGNORE, false];
    }

    #[DataProvider('rules')]
    public function testRuleReturnsBoolean(string $rule, bool $expected): void
    {
        $whitelist = new Whitelist(['App\\' => $rule]);

        self::assertSame($expected, $whitelist->isWhiteListed('App\\Subject'));
        self::assertFalse($whitelist->isWhiteListed('Other\\Subject'));
    }

    public function testLastMatchingRuleWins(): void
    {
        $whitelist = new Whitelist([
            'App\\' => Whitelist::INCLUDE,
            'App\\Internal\\' => Whitelist::IGNORE,
            'App\\Internal\\Public\\' => Whitelist::ANALIZE,
        ]);

        self::assertTrue($whitelist->isWhiteListed('App\\Subject'));
        self::assertFalse($whitelist->isWhiteListed('App\\Internal\\Subject'));
        self::assertTrue($whitelist->isWhiteListed('App\\Internal\\Public\\Subject'));
    }

    public function testAppendAndPrependPreserveRulePriorityAndDuplicateKeys(): void
    {
        $whitelist = new Whitelist(['App\\Internal\\' => Whitelist::IGNORE]);

        self::assertSame($whitelist, $whitelist->prependRule('App\\', Whitelist::INCLUDE));
        self::assertFalse($whitelist->isWhiteListed('App\\Internal\\Subject'));
        self::assertSame($whitelist, $whitelist->appendRule('App\\Internal\\', Whitelist::INCLUDE));
        self::assertFalse($whitelist->isWhiteListed('App\\Internal\\Subject'));
        $whitelist->prependRule('App\\Internal\\', Whitelist::INCLUDE);
        self::assertTrue($whitelist->isWhiteListed('App\\Internal\\Subject'));
    }

    public function testPatternsAreLiteralPrefixes(): void
    {
        $whitelist = new Whitelist(['App.#' => Whitelist::INCLUDE]);

        self::assertTrue($whitelist->isWhiteListed('App.#Subject'));
        self::assertFalse($whitelist->isWhiteListed('AppX#Subject'));
        self::assertFalse($whitelist->isWhiteListed('OtherApp.#Subject'));
        self::assertFalse((new Whitelist([]))->isWhiteListed('App.#Subject'));
    }
}
