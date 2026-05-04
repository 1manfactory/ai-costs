<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Anthropic\AnthropicMessagesUsageExtractor;
use PHPUnit\Framework\TestCase;

final class AnthropicMessagesUsageExtractorTest extends TestCase
{
    public function testItExtractsAnthropicMessagesUsage(): void
    {
        $extractor = new AnthropicMessagesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/anthropic-message.php';
        $usage = $extractor->extract($payload);

        self::assertSame('claude-sonnet-4-6', $usage->model);
        self::assertSame(1800, $usage->inputTokens);
        self::assertSame(500, $usage->cachedInputTokens);
        self::assertSame(200, $usage->cacheWrite5mInputTokens);
        self::assertSame(100, $usage->cacheWrite1hInputTokens);
        self::assertSame(400, $usage->outputTokens);
        self::assertSame('anthropic_messages', $usage->source);
        self::assertSame('standard', $usage->serviceTier);
    }
}
