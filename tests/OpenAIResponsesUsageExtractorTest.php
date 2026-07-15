<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\OpenAI\OpenAIResponsesUsageExtractor;
use PHPUnit\Framework\TestCase;

final class OpenAIResponsesUsageExtractorTest extends TestCase
{
    public function testItExtractsResponsesUsage(): void
    {
        $extractor = new OpenAIResponsesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/openai-responses.php';
        $usage = $extractor->extract($payload);

        self::assertSame('gpt-5.4', $usage->model);
        self::assertSame(1200, $usage->inputTokens);
        self::assertSame(200, $usage->cachedInputTokens);
        self::assertSame(0, $usage->cacheWriteInputTokens);
        self::assertSame(300, $usage->outputTokens);
        self::assertSame(50, $usage->reasoningTokens);
        self::assertSame('responses', $usage->source);
        self::assertSame('default', $usage->serviceTier);
    }

    public function testItExtractsCacheWriteTokensFromResponsesUsage(): void
    {
        $extractor = new OpenAIResponsesUsageExtractor();
        $usage = $extractor->extract(
            [
                'model' => 'gpt-5.6',
                'usage' => [
                    'input_tokens' => 2048,
                    'input_tokens_details' => [
                        'cached_tokens' => 1024,
                        'cache_write_tokens' => 512,
                    ],
                    'output_tokens' => 128,
                ],
            ],
        );

        self::assertSame(512, $usage->cacheWriteInputTokens);
    }
}
