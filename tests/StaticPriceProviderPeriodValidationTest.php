<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Enum\BillingMode;
use AiCosts\Exception\InvalidPricingCatalog;
use AiCosts\Exception\UnavailablePricingPeriod;
use AiCosts\Pricing\StaticPriceProvider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class StaticPriceProviderPeriodValidationTest extends TestCase
{
    public function testItRejectsOverlappingPricingPeriods(): void
    {
        $catalog = $this->validCatalog();
        $periods = $this->sonnetStandardPeriods($catalog);
        $periods[1]['effective_from'] = '2026-08-15';
        $catalog = $this->withSonnetStandardPeriods($catalog, $periods);

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage(
            'Pricing periods overlap for model `claude-sonnet-5` and `standard` billing mode.',
        );

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-08-20'));
    }

    public function testItRejectsAnInvalidPricingPeriodDate(): void
    {
        $catalog = $this->validCatalog();
        $periods = $this->sonnetStandardPeriods($catalog);
        $periods[0]['effective_until'] = '2026-02-31';
        $catalog = $this->withSonnetStandardPeriods($catalog, $periods);

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage(
            'Expected `effective_until` to be a YYYY-MM-DD date for model `claude-sonnet-5`'
            . ' and `standard` billing mode.',
        );

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-08-31'));
    }

    public function testItRejectsReversedPricingPeriods(): void
    {
        $catalog = $this->validCatalog();
        $periods = $this->sonnetStandardPeriods($catalog);
        $periods[0] = [
            'effective_from' => '2026-09-02',
            'effective_until' => '2026-09-01',
            'prices' => $this->introductorySonnetPrices(),
        ];
        $catalog = $this->withSonnetStandardPeriods($catalog, $periods);

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage(
            'Pricing period dates are reversed for model `claude-sonnet-5` and `standard` billing mode.',
        );

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-09-01'));
    }

    public function testItThrowsWhenNoPricingPeriodMatchesTheSelectedDate(): void
    {
        $catalog = $this->validCatalog();
        $catalog = $this->withSonnetStandardPeriods($catalog, [
            [
                'effective_until' => '2026-08-30',
                'prices' => $this->introductorySonnetPrices(),
            ],
            [
                'effective_from' => '2026-09-02',
                'prices' => $this->regularSonnetPrices(),
            ],
        ]);

        $provider = new StaticPriceProvider($catalog, new DateTimeImmutable('2026-09-01'));

        $this->expectException(UnavailablePricingPeriod::class);
        $this->expectExceptionMessage(
            'No pricing period matches `2026-09-01` for model `claude-sonnet-5` and `standard` billing mode.',
        );

        $provider->get('claude-sonnet-5', BillingMode::STANDARD);
    }

    /**
     * @return array{
     *     version: string,
     *     as_of: string,
     *     source_urls: list<string>,
     *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
     *     models: array<string, array<string, mixed>>
     * }
     */
    private function validCatalog(): array
    {
        /** @var array{
         *     version: string,
         *     as_of: string,
         *     source_urls: list<string>,
         *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
         *     models: array<string, array<string, mixed>>
         * } $catalog
         */
        $catalog = require dirname(__DIR__) . '/resources/pricing/catalog.php';

        return $catalog;
    }

    /**
     * @param array{
     *     models: array<string, array<string, mixed>>
     * } $catalog
     * @return list<array<string, mixed>>
     */
    private function sonnetStandardPeriods(array $catalog): array
    {
        $model = $catalog['models']['claude-sonnet-5'] ?? null;

        if (!is_array($model)) {
            throw new RuntimeException('Expected claude-sonnet-5 model definition.');
        }

        $cards = $model['cards'] ?? null;

        if (!is_array($cards)) {
            throw new RuntimeException('Expected claude-sonnet-5 cards definition.');
        }

        $standard = $cards['standard'] ?? null;

        if (!is_array($standard)) {
            throw new RuntimeException('Expected claude-sonnet-5 standard card definition.');
        }

        $periods = $standard['periods'] ?? null;

        if (!is_array($periods)) {
            throw new RuntimeException('Expected claude-sonnet-5 standard periods definition.');
        }

        /** @var list<array<string, mixed>> $normalizedPeriods */
        $normalizedPeriods = $periods;

        return $normalizedPeriods;
    }

    /**
     * @param array{
     *     version: string,
     *     as_of: string,
     *     source_urls: list<string>,
     *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
     *     models: array<string, array<string, mixed>>
     * } $catalog
     * @param list<array<string, mixed>> $periods
     * @return array{
     *     version: string,
     *     as_of: string,
     *     source_urls: list<string>,
     *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
     *     models: array<string, array<string, mixed>>
     * }
     */
    private function withSonnetStandardPeriods(array $catalog, array $periods): array
    {
        $model = $catalog['models']['claude-sonnet-5'];
        $cards = $model['cards'] ?? null;

        if (!is_array($cards)) {
            throw new RuntimeException('Expected claude-sonnet-5 cards definition.');
        }

        $standard = $cards['standard'] ?? [];

        if (!is_array($standard)) {
            throw new RuntimeException('Expected claude-sonnet-5 standard card definition.');
        }

        $standard['periods'] = $periods;
        $cards['standard'] = $standard;
        $model['cards'] = $cards;
        $catalog['models']['claude-sonnet-5'] = $model;

        return $catalog;
    }

    /**
     * @return array<string, int>
     */
    private function introductorySonnetPrices(): array
    {
        return [
            'input_usd_microcent_per_million_tokens' => 200_000,
            'cached_input_usd_microcent_per_million_tokens' => 20_000,
            'cache_write_5m_input_usd_microcent_per_million_tokens' => 250_000,
            'cache_write_1h_input_usd_microcent_per_million_tokens' => 400_000,
            'output_usd_microcent_per_million_tokens' => 1_000_000,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function regularSonnetPrices(): array
    {
        return [
            'input_usd_microcent_per_million_tokens' => 300_000,
            'cached_input_usd_microcent_per_million_tokens' => 30_000,
            'cache_write_5m_input_usd_microcent_per_million_tokens' => 375_000,
            'cache_write_1h_input_usd_microcent_per_million_tokens' => 600_000,
            'output_usd_microcent_per_million_tokens' => 1_500_000,
        ];
    }
}
