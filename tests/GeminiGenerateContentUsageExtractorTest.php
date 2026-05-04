<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Gemini\GeminiGenerateContentUsageExtractor;
use PHPUnit\Framework\TestCase;

final class GeminiGenerateContentUsageExtractorTest extends TestCase
{
    public function testItExtractsGeminiGenerateContentUsage(): void
    {
        $extractor = new GeminiGenerateContentUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/gemini-generate-content.php';
        $usage = $extractor->extract($payload);

        self::assertSame('gemini-2.5-pro-001', $usage->model);
        self::assertSame(1200, $usage->inputTokens);
        self::assertSame(200, $usage->cachedInputTokens);
        self::assertSame(300, $usage->outputTokens);
        self::assertSame(80, $usage->reasoningTokens);
        self::assertSame('gemini_generate_content', $usage->source);
    }
}
