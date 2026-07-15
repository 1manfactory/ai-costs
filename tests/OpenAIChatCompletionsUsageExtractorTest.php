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
        self::assertSame(0, $usage->cacheWriteInputTokens);
        self::assertSame(600, $usage->outputTokens);
        self::assertSame(120, $usage->reasoningTokens);
        self::assertSame('chat_completions', $usage->source);
        self::assertSame('default', $usage->serviceTier);
    }

    public function testItExtractsDocumentedCacheWriteTokensFromChatCompletionsUsage(): void
    {
        $extractor = new OpenAIChatCompletionsUsageExtractor();
        $usage = $extractor->extract(
            [
                'model' => 'gpt-5.6',
                'usage' => [
                    'prompt_tokens' => 2006,
                    'prompt_tokens_details' => [
                        'cached_tokens' => 1920,
                        'cache_write_tokens' => 0,
                    ],
                    'completion_tokens' => 300,
                    'completion_tokens_details' => [
                        'reasoning_tokens' => 0,
                    ],
                ],
            ],
        );

        self::assertSame(0, $usage->cacheWriteInputTokens);
    }
}
