<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Contract\PriceProviderInterface;
use AiCosts\Exception\InvalidPricingCatalog;
use AiCosts\Pricing\StaticPriceProvider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class StaticPriceProviderValidationTest extends TestCase
{
    public function testItRejectsAnEmptyCatalogVersion(): void
    {
        $catalog = $this->validCatalog();
        $catalog['version'] = '';

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Pricing catalog version must be a non-empty string.');

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-07-15'));
    }

    public function testItRejectsAnInvalidVerifiedAtDate(): void
    {
        $catalog = $this->validCatalog();
        $catalog['providers'] = $this->catalogWithProviderDate($catalog['providers'], 'openai', '2026/07/15');

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Provider `openai` must have a verified_at date in YYYY-MM-DD format.');

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-07-15'));
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

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-07-15'));
    }

    public function testItRejectsDuplicateGlobalSourceUrls(): void
    {
        $catalog = $this->validCatalog();
        $catalog['source_urls'][] = 'https://developers.openai.com/api/docs/pricing';

        $this->expectException(InvalidPricingCatalog::class);
        $this->expectExceptionMessage('Pricing catalog source URLs must not contain duplicate URLs.');

        new StaticPriceProvider($catalog, new DateTimeImmutable('2026-07-15'));
    }

    public function testUnboundedPriceCardsStillWork(): void
    {
        $provider = StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
        $card = $provider->get('gpt-5.4', \AiCosts\Enum\BillingMode::STANDARD);

        self::assertSame('gpt-5.4', $card->model);
        self::assertSame(250_000, $card->inputRateInUsdMicrocentPerMillionTokens);
    }

    public function testPriceProviderInterfaceRemainsUnchanged(): void
    {
        $reflection = new ReflectionClass(PriceProviderInterface::class);
        $methodNames = array_map(
            static fn (\ReflectionMethod $method): string => $method->getName(),
            $reflection->getMethods(),
        );
        sort($methodNames);

        self::assertSame(['asOf', 'get', 'pricedModels'], $methodNames);
    }

    public function testStaticPriceProviderDefaultWithoutArgumentRemainsValid(): void
    {
        self::assertInstanceOf(StaticPriceProvider::class, StaticPriceProvider::default());
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
