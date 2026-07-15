<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\Enum\ContextPricingMode;
use AiCosts\Exception\UnsupportedUsageScenario;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\BillingContext;
use AiCosts\Value\UsageBreakdown;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OpenAIGpt55ProCostCalculatorTest extends TestCase
{
    public function testItUsesShortContextForGpt55ProInAutoModeWithoutThreshold(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5-pro',
            inputTokens: 400_000,
            cachedInputTokens: 0,
            outputTokens: 1_000_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertSame(1_200_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(18_000_000, $breakdown->outputCostInUsdMicrocent);
    }

    public function testItUsesShortContextForGpt55ProWhenForced(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5-pro',
            inputTokens: 1_000_000,
            cachedInputTokens: 0,
            outputTokens: 1_000_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::STANDARD,
                contextPricingMode: ContextPricingMode::SHORT,
            ),
        );

        self::assertSame(3_000_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(18_000_000, $breakdown->outputCostInUsdMicrocent);
    }

    public function testItUsesExplicitLongContextForGpt55ProStandard(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5-pro',
            inputTokens: 1_000_000,
            cachedInputTokens: 0,
            outputTokens: 1_000_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::STANDARD,
                contextPricingMode: ContextPricingMode::LONG,
            ),
        );

        self::assertSame(6_000_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(27_000_000, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(33_000_000, $breakdown->totalCostInUsdMicrocent);
    }

    public function testItRejectsExplicitLongContextForGpt55ProBatch(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5-pro',
            inputTokens: 1_000_000,
            cachedInputTokens: 0,
            outputTokens: 1_000,
        );

        $this->expectException(UnsupportedUsageScenario::class);
        $this->expectExceptionMessage(
            'Long-context input pricing is not configured for model `gpt-5.5-pro` in `batch` billing mode.',
        );

        (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::BATCH,
                contextPricingMode: ContextPricingMode::LONG,
            ),
        );
    }

    public function testItRejectsExplicitLongContextForGpt55ProFlex(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5-pro',
            inputTokens: 1_000_000,
            cachedInputTokens: 0,
            outputTokens: 1_000,
        );

        $this->expectException(UnsupportedUsageScenario::class);
        $this->expectExceptionMessage(
            'Long-context input pricing is not configured for model `gpt-5.5-pro` in `flex` billing mode.',
        );

        (new CostCalculator($this->provider()))->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::FLEX,
                contextPricingMode: ContextPricingMode::LONG,
            ),
        );
    }

    private function provider(): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
    }
}
