<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Anthropic\AnthropicMessagesUsageExtractor;
use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\Gemini\GeminiGenerateContentUsageExtractor;
use AiCosts\OpenAI\OpenAIResponsesUsageExtractor;
use AiCosts\OpenAI\OpenAIToolPricing;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\BillingContext;
use AiCosts\Value\UsageBreakdown;
use PHPUnit\Framework\TestCase;

final class CostCalculatorTest extends TestCase
{
    public function testItCalculatesCostsFromAResponsesPayload(): void
    {
        $extractor = new OpenAIResponsesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/openai-responses.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::STANDARD,
                additionalCharges: [
                    OpenAIToolPricing::webSearchCalls(3),
                ],
            ),
        );

        self::assertSame(2500, $breakdown->inputCostInUsdMicros);
        self::assertSame(50, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(4500, $breakdown->outputCostInUsdMicros);
        self::assertSame(37050, $breakdown->totalCostInUsdMicros);
        self::assertSame('2026-05-02', $breakdown->pricingAsOf);
        self::assertSame('2026-05-02', $breakdown->toArray()['pricing_as_of']);
        self::assertSame(37050, $breakdown->toArray()['total_cost_in_usd_micros']);
    }

    public function testItCalculatesCostsFromAnAnthropicMessagesPayload(): void
    {
        $extractor = new AnthropicMessagesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/anthropic-message.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(3000, $breakdown->inputCostInUsdMicros);
        self::assertSame(150, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(750, $breakdown->cacheWrite5mInputCostInUsdMicros);
        self::assertSame(600, $breakdown->cacheWrite1hInputCostInUsdMicros);
        self::assertSame(6000, $breakdown->outputCostInUsdMicros);
        self::assertSame(10500, $breakdown->totalCostInUsdMicros);
    }

    public function testItCalculatesCostsFromAGeminiGenerateContentPayload(): void
    {
        $extractor = new GeminiGenerateContentUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/gemini-generate-content.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(1250, $breakdown->inputCostInUsdMicros);
        self::assertSame(25, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(3000, $breakdown->outputCostInUsdMicros);
        self::assertSame(4275, $breakdown->totalCostInUsdMicros);
    }

    public function testItAppliesLongContextPricingForVersionedModelIds(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.4-2026-03-05',
            inputTokens: 300000,
            cachedInputTokens: 0,
            outputTokens: 1000,
        );

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertTrue($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(1500000, $breakdown->inputCostInUsdMicros);
        self::assertSame(22500, $breakdown->outputCostInUsdMicros);
        self::assertSame(1522500, $breakdown->totalCostInUsdMicros);
    }

    public function testItAppliesLongContextPricingForVersionedGeminiModelIds(): void
    {
        $usage = new UsageBreakdown(
            model: 'gemini-2.5-pro-001',
            inputTokens: 250000,
            cachedInputTokens: 50000,
            outputTokens: 1000,
        );

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertTrue($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(500000, $breakdown->inputCostInUsdMicros);
        self::assertSame(12500, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(15000, $breakdown->outputCostInUsdMicros);
        self::assertSame(527500, $breakdown->totalCostInUsdMicros);
    }

    public function testItCalculatesCostsForNewlyAddedOpenAiLegacyModels(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-4.1-2025-04-14',
            inputTokens: 2000,
            cachedInputTokens: 500,
            outputTokens: 250,
        );

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(3000, $breakdown->inputCostInUsdMicros);
        self::assertSame(250, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(2000, $breakdown->outputCostInUsdMicros);
        self::assertSame(5250, $breakdown->totalCostInUsdMicros);
    }

    public function testItCalculatesCostsForVersionedGpt5MiniModels(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5-mini-2025-08-07',
            inputTokens: 2000,
            cachedInputTokens: 500,
            outputTokens: 250,
        );

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(375, $breakdown->inputCostInUsdMicros);
        self::assertSame(13, $breakdown->cachedInputCostInUsdMicros);
        self::assertSame(500, $breakdown->outputCostInUsdMicros);
        self::assertSame(888, $breakdown->totalCostInUsdMicros);
    }
}
