<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Exception\UnknownProvider;
use AiCosts\Pricing\StaticPriceProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class StaticPriceProviderMetadataTest extends TestCase
{
    public function testItExposesTheCatalogVersion(): void
    {
        $catalog = StaticPriceProvider::default();

        self::assertSame('2026-07-08', $catalog->version);
    }

    public function testItExposesOpenAiProviderMetadata(): void
    {
        $metadata = StaticPriceProvider::default()->provider('openai');

        self::assertSame('2026-07-08', $metadata->verifiedAt);
        self::assertSame(
            [
                'https://developers.openai.com/api/docs/pricing',
                'https://developers.openai.com/api/docs/models/gpt-5.4/',
                'https://developers.openai.com/api/docs/models/gpt-5.4-pro',
            ],
            $metadata->sourceUrls,
        );
    }

    public function testItExposesAnthropicProviderMetadata(): void
    {
        $metadata = StaticPriceProvider::default()->provider('anthropic');

        self::assertSame('2026-07-08', $metadata->verifiedAt);
    }

    public function testItExposesGeminiProviderMetadata(): void
    {
        $metadata = StaticPriceProvider::default()->provider('gemini');

        self::assertSame('2026-07-08', $metadata->verifiedAt);
    }

    public function testGlobalSourcesContainAllProviderSourcesWithoutDuplicates(): void
    {
        $catalog = StaticPriceProvider::default();
        $providerSourceUrls = array_merge(
            $catalog->provider('openai')->sourceUrls,
            $catalog->provider('anthropic')->sourceUrls,
            $catalog->provider('gemini')->sourceUrls,
        );

        self::assertSame($catalog->sourceUrls, array_values(array_unique($catalog->sourceUrls)));

        foreach ($providerSourceUrls as $providerSourceUrl) {
            self::assertContains($providerSourceUrl, $catalog->sourceUrls);
        }
    }

    public function testItThrowsForAnUnknownProvider(): void
    {
        $catalog = StaticPriceProvider::default();

        $this->expectException(UnknownProvider::class);
        $this->expectExceptionMessage(
            'Unknown provider `unknown`. Supported providers: anthropic, gemini, openai.',
        );

        $catalog->provider('unknown');
    }

    public function testProviderMetadataIsImmutable(): void
    {
        $metadata = StaticPriceProvider::default()->provider('openai');
        $reflection = new ReflectionClass($metadata);

        self::assertTrue($reflection->isReadOnly());
        self::assertTrue($reflection->getProperty('name')->isReadOnly());
        self::assertTrue($reflection->getProperty('verifiedAt')->isReadOnly());
        self::assertTrue($reflection->getProperty('sourceUrls')->isReadOnly());
    }
}
