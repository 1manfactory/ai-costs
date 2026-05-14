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

        self::assertSame(250, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(5, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(450, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(3705, $breakdown->totalCostInUsdMicrocent);
        self::assertSame('2026-05-13', $breakdown->pricingAsOf);
        self::assertSame('2026-05-13', $breakdown->toArray()['pricing_as_of']);
        self::assertSame(3705, $breakdown->toArray()['total_cost_in_usd_microcent']);
    }

    public function testItCalculatesCostsFromAnAnthropicMessagesPayload(): void
    {
        $extractor = new AnthropicMessagesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/anthropic-message.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(300, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(15, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(75, $breakdown->cacheWrite5mInputCostInUsdMicrocent);
        self::assertSame(60, $breakdown->cacheWrite1hInputCostInUsdMicrocent);
        self::assertSame(600, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(1050, $breakdown->totalCostInUsdMicrocent);
    }

    public function testItCalculatesCostsFromAGeminiGenerateContentPayload(): void
    {
        $extractor = new GeminiGenerateContentUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/gemini-generate-content.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator(StaticPriceProvider::default());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(125, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(3, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(300, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(428, $breakdown->totalCostInUsdMicrocent);
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
        self::assertSame(150000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(2250, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(152250, $breakdown->totalCostInUsdMicrocent);
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
        self::assertSame(50000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(1250, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(1500, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(52750, $breakdown->totalCostInUsdMicrocent);
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

        self::assertSame(300, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(25, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(200, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(525, $breakdown->totalCostInUsdMicrocent);
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

        self::assertSame(38, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(1, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(50, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(89, $breakdown->totalCostInUsdMicrocent);
    }
}
