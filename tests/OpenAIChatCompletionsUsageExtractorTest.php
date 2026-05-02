<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\OpenAI\OpenAIChatCompletionsUsageExtractor;
use PHPUnit\Framework\TestCase;

final class OpenAIChatCompletionsUsageExtractorTest extends TestCase
{
    public function testItExtractsChatCompletionUsage(): void
    {
        $extractor = new OpenAIChatCompletionsUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/openai-chat-completions.php';
        $usage = $extractor->extract($payload);

        self::assertSame('gpt-5.5', $usage->model);
        self::assertSame(1500, $usage->inputTokens);
        self::assertSame(500, $usage->cachedInputTokens);
        self::assertSame(600, $usage->outputTokens);
        self::assertSame(120, $usage->reasoningTokens);
        self::assertSame('chat_completions', $usage->source);
        self::assertSame('default', $usage->serviceTier);
    }
}
