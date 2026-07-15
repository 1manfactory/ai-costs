<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Exception\UnknownProvider;
use AiCosts\Pricing\StaticPriceProvider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class StaticPriceProviderMetadataTest extends TestCase
{
    public function testItExposesTheCatalogVersion(): void
    {
        $catalog = $this->provider();

        self::assertSame('2026-07-15', $catalog->version);
    }

    public function testItExposesOpenAiProviderMetadata(): void
    {
        $metadata = $this->provider()->provider('openai');

        self::assertSame('2026-07-15', $metadata->verifiedAt);
        self::assertSame(
            [
                'https://developers.openai.com/api/docs/pricing',
                'https://developers.openai.com/api/docs/guides/prompt-caching',
                'https://developers.openai.com/api/docs/models/gpt-5.6-sol',
                'https://developers.openai.com/api/docs/models/gpt-5.6-terra',
                'https://developers.openai.com/api/docs/models/gpt-5.6-luna',
                'https://developers.openai.com/api/docs/models/gpt-5.5-pro',
            ],
            $metadata->sourceUrls,
        );
    }

    public function testItExposesAnthropicProviderMetadata(): void
    {
        $metadata = $this->provider()->provider('anthropic');

        self::assertSame('2026-07-15', $metadata->verifiedAt);
    }

    public function testItExposesGeminiProviderMetadata(): void
    {
        $metadata = $this->provider()->provider('gemini');

        self::assertSame('2026-07-15', $metadata->verifiedAt);
    }

    public function testGlobalSourcesContainAllProviderSourcesWithoutDuplicates(): void
    {
        $catalog = $this->provider();
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
        $catalog = $this->provider();

        $this->expectException(UnknownProvider::class);
        $this->expectExceptionMessage(
            'Unknown provider `unknown`. Supported providers: anthropic, gemini, openai.',
        );

        $catalog->provider('unknown');
    }

    public function testProviderMetadataIsImmutable(): void
    {
        $metadata = $this->provider()->provider('openai');
        $reflection = new ReflectionClass($metadata);

        self::assertTrue($reflection->isReadOnly());
        self::assertTrue($reflection->getProperty('name')->isReadOnly());
        self::assertTrue($reflection->getProperty('verifiedAt')->isReadOnly());
        self::assertTrue($reflection->getProperty('sourceUrls')->isReadOnly());
    }

    private function provider(): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
    }
}
