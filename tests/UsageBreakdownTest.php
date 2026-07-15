<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Value\UsageBreakdown;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UsageBreakdownTest extends TestCase
{
    public function testGenericCacheWriteTokensMustNotBeNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cacheWriteInputTokens must be >= 0.');

        new UsageBreakdown(
            model: 'gpt-5.6',
            inputTokens: 100,
            cachedInputTokens: 0,
            outputTokens: 0,
            cacheWriteInputTokens: -1,
        );
    }

    public function testInputTokenSubcategoriesCannotExceedInputTokens(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The sum of cached and cache-write input tokens cannot be greater than inputTokens.',
        );

        new UsageBreakdown(
            model: 'gpt-5.6',
            inputTokens: 100,
            cachedInputTokens: 20,
            outputTokens: 0,
            cacheWriteInputTokens: 30,
            cacheWrite5mInputTokens: 30,
            cacheWrite1hInputTokens: 30,
        );
    }

    public function testExistingConstructorCallsRemainValid(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.4',
            inputTokens: 100,
            cachedInputTokens: 10,
            outputTokens: 5,
        );

        self::assertSame(0, $usage->cacheWriteInputTokens);
        self::assertSame(90, $usage->uncachedInputTokens());
    }
}
