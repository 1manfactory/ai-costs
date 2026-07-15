<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Enum\BillingMode;
use AiCosts\Pricing\StaticPriceProvider;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StaticPriceProviderOpenAiCatalogTest extends TestCase
{
    #[DataProvider('gpt56ModelProvider')]
    public function testAllGpt56ModelsArePriced(string $model): void
    {
        $card = $this->provider()->get($model, BillingMode::STANDARD);

        self::assertSame($model, $card->model);
    }

    #[DataProvider('gpt56RateProvider')]
    public function testGpt56RatesMatchTheCatalog(
        string $model,
        BillingMode $billingMode,
        int $inputRate,
        int $cachedInputRate,
        ?int $cacheWriteRate,
        int $outputRate,
    ): void {
        $card = $this->provider()->get($model, $billingMode);

        self::assertSame($inputRate, $card->inputRateInUsdMicrocentPerMillionTokens);
        self::assertSame($cachedInputRate, $card->cachedInputRateInUsdMicrocentPerMillionTokens);
        self::assertSame($cacheWriteRate, $card->cacheWriteInputRateInUsdMicrocentPerMillionTokens);
        self::assertSame($outputRate, $card->outputRateInUsdMicrocentPerMillionTokens);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function gpt56ModelProvider(): array
    {
        return [
            'sol' => ['gpt-5.6-sol'],
            'terra' => ['gpt-5.6-terra'],
            'luna' => ['gpt-5.6-luna'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: BillingMode, 2: int, 3: int, 4: ?int, 5: int}>
     */
    public static function gpt56RateProvider(): array
    {
        return [
            'sol-standard' => ['gpt-5.6-sol', BillingMode::STANDARD, 500_000, 50_000, 625_000, 3_000_000],
            'sol-batch' => ['gpt-5.6-sol', BillingMode::BATCH, 250_000, 25_000, 312_500, 1_500_000],
            'sol-flex' => ['gpt-5.6-sol', BillingMode::FLEX, 250_000, 25_000, 312_500, 1_500_000],
            'sol-priority' => ['gpt-5.6-sol', BillingMode::PRIORITY, 1_000_000, 100_000, 1_250_000, 6_000_000],
            'terra-standard' => ['gpt-5.6-terra', BillingMode::STANDARD, 250_000, 25_000, 312_500, 1_500_000],
            'terra-batch' => ['gpt-5.6-terra', BillingMode::BATCH, 125_000, 12_500, 156_250, 750_000],
            'terra-flex' => ['gpt-5.6-terra', BillingMode::FLEX, 125_000, 12_500, 156_250, 750_000],
            'terra-priority' => ['gpt-5.6-terra', BillingMode::PRIORITY, 500_000, 50_000, 625_000, 3_000_000],
            'luna-standard' => ['gpt-5.6-luna', BillingMode::STANDARD, 100_000, 10_000, 125_000, 600_000],
            'luna-batch' => ['gpt-5.6-luna', BillingMode::BATCH, 50_000, 5_000, 62_500, 300_000],
            'luna-flex' => ['gpt-5.6-luna', BillingMode::FLEX, 50_000, 5_000, 62_500, 300_000],
            'luna-priority' => ['gpt-5.6-luna', BillingMode::PRIORITY, 200_000, 20_000, 250_000, 1_200_000],
        ];
    }

    private function provider(): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
    }
}
