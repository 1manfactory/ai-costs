<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\BillingContext;
use AiCosts\Value\UsageBreakdown;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AnthropicCostCalculatorTest extends TestCase
{
    public function testItCalculatesClaudeFable5StandardAndBatch(): void
    {
        $usage = new UsageBreakdown(
            model: 'claude-fable-5',
            inputTokens: 1_000_000,
            cachedInputTokens: 100_000,
            outputTokens: 100_000,
        );

        $standard = (new CostCalculator($this->provider()))->calculate($usage);
        $batch = (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(billingMode: BillingMode::BATCH),
        );

        self::assertSame(900_000, $standard->inputCostInUsdMicrocent);
        self::assertSame(10_000, $standard->cachedInputCostInUsdMicrocent);
        self::assertSame(500_000, $standard->outputCostInUsdMicrocent);
        self::assertSame(450_000, $batch->inputCostInUsdMicrocent);
        self::assertSame(5_000, $batch->cachedInputCostInUsdMicrocent);
        self::assertSame(250_000, $batch->outputCostInUsdMicrocent);
    }

    public function testItCalculatesClaudeOpus48StandardAndBatch(): void
    {
        $usage = new UsageBreakdown(
            model: 'claude-opus-4-8',
            inputTokens: 1_000_000,
            cachedInputTokens: 100_000,
            outputTokens: 100_000,
        );

        $standard = (new CostCalculator($this->provider()))->calculate($usage);
        $batch = (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(billingMode: BillingMode::BATCH),
        );

        self::assertSame(450_000, $standard->inputCostInUsdMicrocent);
        self::assertSame(5_000, $standard->cachedInputCostInUsdMicrocent);
        self::assertSame(250_000, $standard->outputCostInUsdMicrocent);
        self::assertSame(225_000, $batch->inputCostInUsdMicrocent);
        self::assertSame(2_500, $batch->cachedInputCostInUsdMicrocent);
        self::assertSame(125_000, $batch->outputCostInUsdMicrocent);
    }

    public function testItUsesClaudeSonnet5IntroductoryPricingOnAugust31(): void
    {
        $usage = new UsageBreakdown(
            model: 'claude-sonnet-5',
            inputTokens: 1_000_000,
            cachedInputTokens: 100_000,
            outputTokens: 100_000,
            cacheWrite5mInputTokens: 100_000,
            cacheWrite1hInputTokens: 100_000,
        );

        $breakdown = (new CostCalculator($this->provider('2026-08-31')))->calculate($usage);

        self::assertSame(140_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(2_000, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(25_000, $breakdown->cacheWrite5mInputCostInUsdMicrocent);
        self::assertSame(40_000, $breakdown->cacheWrite1hInputCostInUsdMicrocent);
        self::assertSame(100_000, $breakdown->outputCostInUsdMicrocent);
    }

    public function testItUsesClaudeSonnet5RegularPricingOnSeptember1(): void
    {
        $usage = new UsageBreakdown(
            model: 'claude-sonnet-5',
            inputTokens: 1_000_000,
            cachedInputTokens: 100_000,
            outputTokens: 100_000,
            cacheWrite5mInputTokens: 100_000,
            cacheWrite1hInputTokens: 100_000,
        );

        $breakdown = (new CostCalculator($this->provider('2026-09-01')))->calculate($usage);

        self::assertSame(210_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(3_000, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(37_500, $breakdown->cacheWrite5mInputCostInUsdMicrocent);
        self::assertSame(60_000, $breakdown->cacheWrite1hInputCostInUsdMicrocent);
        self::assertSame(150_000, $breakdown->outputCostInUsdMicrocent);
    }

    public function testClaudeLargePromptsDoNotTriggerLongContextUpliftWithoutConfiguredRates(): void
    {
        $usage = new UsageBreakdown(
            model: 'claude-fable-5',
            inputTokens: 900_000,
            cachedInputTokens: 0,
            outputTokens: 10_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertFalse($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(900_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(50_000, $breakdown->outputCostInUsdMicrocent);
    }

    private function provider(string $pricingDate = '2026-07-15'): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable($pricingDate));
    }
}
