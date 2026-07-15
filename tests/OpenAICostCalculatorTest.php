<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\Exception\UnsupportedUsageScenario;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\UsageBreakdown;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OpenAICostCalculatorTest extends TestCase
{
    public function testItAppliesLongContextPricingForVersionedModelIds(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.4-2026-03-05',
            inputTokens: 300_000,
            cachedInputTokens: 0,
            outputTokens: 1_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertTrue($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(150_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(2_250, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(152_250, $breakdown->totalCostInUsdMicrocent);
    }

    public function testGpt56AliasResolvesToSol(): void
    {
        $card = $this->provider()->get('gpt-5.6', BillingMode::STANDARD);

        self::assertSame('gpt-5.6-sol', $card->model);
    }

    public function testItCalculatesShortContextPricingForGpt56AtThreshold(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.6',
            inputTokens: 272_000,
            cachedInputTokens: 0,
            outputTokens: 1_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertFalse($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(136_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(3_000, $breakdown->outputCostInUsdMicrocent);
    }

    public function testItCalculatesLongContextPricingForGpt56AboveThreshold(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.6',
            inputTokens: 272_001,
            cachedInputTokens: 0,
            outputTokens: 1_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertTrue($breakdown->priceCard->usesLongContext($usage));
        self::assertSame(272_001, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(4_500, $breakdown->outputCostInUsdMicrocent);
    }

    public function testItCalculatesGenericOpenAiCacheWriteCostsSeparately(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.6',
            inputTokens: 300_000,
            cachedInputTokens: 20_000,
            outputTokens: 10_000,
            cacheWriteInputTokens: 30_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertSame(250_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(2_000, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(37_500, $breakdown->cacheWriteInputCostInUsdMicrocent);
        self::assertSame(45_000, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(334_500, $breakdown->totalCostInUsdMicrocent);
    }

    public function testItUsesLongContextCacheWriteRatesForGpt56(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.6-terra',
            inputTokens: 300_000,
            cachedInputTokens: 0,
            outputTokens: 0,
            cacheWriteInputTokens: 40_000,
        );

        $breakdown = (new CostCalculator($this->provider()))->calculate($usage);

        self::assertSame(130_000, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(25_000, $breakdown->cacheWriteInputCostInUsdMicrocent);
    }

    public function testItRejectsGenericCacheWritesWhenTheModelDoesNotSupportThem(): void
    {
        $usage = new UsageBreakdown(
            model: 'gpt-5.5',
            inputTokens: 2_000,
            cachedInputTokens: 0,
            outputTokens: 0,
            cacheWriteInputTokens: 100,
        );

        $this->expectException(UnsupportedUsageScenario::class);
        $this->expectExceptionMessage(
            'Generic cache-write pricing is not configured for model `gpt-5.5` in `standard` billing mode.',
        );

        (new CostCalculator($this->provider()))->calculate($usage);
    }

    private function provider(): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
    }
}
