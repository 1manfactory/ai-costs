<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
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
        self::assertSame(37050, $breakdown->toArray()['total_cost_in_usd_micros']);
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
}
