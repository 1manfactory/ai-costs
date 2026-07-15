<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Exception\InvalidPricingCatalog;
use AiCosts\Pricing\StaticPriceProvider;
use PHPUnit\Framework\TestCase;

final class StaticPriceProviderValidationTest extends TestCase
{
    public function testItRejectsAnEmptyCatalogVersion(): void
    {
        $catalog = $this->validCatalog();
        $catalog['version'] = '';

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Pricing catalog version must be a non-empty string.');

        new StaticPriceProvider($catalog);
    }

    public function testItRejectsAnInvalidVerifiedAtDate(): void
    {
        $catalog = $this->validCatalog();
        $catalog['providers'] = $this->catalogWithProviderDate(
            $catalog['providers'],
            'openai',
            '2026/07/08',
        );

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Provider `openai` must have a verified_at date in YYYY-MM-DD format.');

        new StaticPriceProvider($catalog);
    }

    public function testItRejectsAnInvalidSourceUrl(): void
    {
        $catalog = $this->validCatalog();
        $catalog['providers'] = $this->catalogWithProviderSources(
            $catalog['providers'],
            'gemini',
            ['http://ai.google.dev/gemini-api/docs/pricing'],
        );

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage(
            'Provider `gemini` source URLs must contain only valid absolute HTTPS URLs.',
        );

        new StaticPriceProvider($catalog);
    }

    public function testItRejectsDuplicateGlobalSourceUrls(): void
    {
        $catalog = $this->validCatalog();
        $catalog['source_urls'][] = 'https://developers.openai.com/api/docs/pricing';

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Pricing catalog source URLs must not contain duplicate URLs.');

        new StaticPriceProvider($catalog);
    }

    /**
     * @return array{
     *     version: string,
     *     as_of: string,
     *     source_urls: list<string>,
     *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
     *     models: array<string, mixed>
     * }
     */
    private function validCatalog(): array
    {
        /** @var array{
         *     version: string,
         *     as_of: string,
         *     source_urls: list<string>,
         *     providers: array<string, array{name: string, verified_at: string, source_urls: list<string>}>,
         *     models: array<string, mixed>
         * } $catalog
         */
        $catalog = require dirname(__DIR__) . '/resources/pricing/catalog.php';

        return $catalog;
    }

    /**
     * @param array<string, array{name: string, verified_at: string, source_urls: list<string>}> $providers
     * @return array<string, array{name: string, verified_at: string, source_urls: list<string>}>
     */
    private function catalogWithProviderDate(array $providers, string $providerName, string $verifiedAt): array
    {
        $provider = $providers[$providerName];
        $provider['verified_at'] = $verifiedAt;
        $providers[$providerName] = $provider;

        return $providers;
    }

    /**
     * @param array<string, array{name: string, verified_at: string, source_urls: list<string>}> $providers
     * @param list<string> $sourceUrls
     * @return array<string, array{name: string, verified_at: string, source_urls: list<string>}>
     */
    private function catalogWithProviderSources(array $providers, string $providerName, array $sourceUrls): array
    {
        $provider = $providers[$providerName];
        $provider['source_urls'] = $sourceUrls;
        $providers[$providerName] = $provider;

        return $providers;
    }
}
