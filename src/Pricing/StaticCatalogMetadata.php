<?php

declare(strict_types=1);

namespace AiCosts\Pricing;

use AiCosts\Exception\InvalidPricingCatalog;
use AiCosts\Value\ProviderMetadata;
use DateTimeImmutable;
use DateTimeZone;

final readonly class StaticCatalogMetadata
{
    public string $version;

    /**
     * @var list<string>
     */
    public array $sourceUrls;

    /**
     * @var array<string, ProviderMetadata>
     */
    private array $providers;

    /**
     * @param array<string, mixed> $catalog
     */
    public function __construct(array $catalog)
    {
        $this->version = $this->validateVersion($catalog['version'] ?? null);
        $this->sourceUrls = $this->validateSourceUrls($catalog['source_urls'] ?? null, 'Pricing catalog source URLs');
        $this->providers = $this->validateProviders($catalog['providers'] ?? null);
    }

    public function provider(string $name): ?ProviderMetadata
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * @return list<string>
     */
    private function validateSourceUrls(mixed $sourceUrls, string $label): array
    {
        if (!is_array($sourceUrls)) {
            throw new InvalidPricingCatalog(sprintf('%s must be a list of HTTPS URLs.', $label));
        }

        $normalized = [];

        foreach ($sourceUrls as $sourceUrl) {
            if (!is_string($sourceUrl) || !$this->isValidHttpsUrl($sourceUrl)) {
                throw new InvalidPricingCatalog(sprintf('%s must contain only valid absolute HTTPS URLs.', $label));
            }

            $normalized[] = $sourceUrl;
        }

        if (count($normalized) !== count(array_unique($normalized))) {
            throw new InvalidPricingCatalog(sprintf('%s must not contain duplicate URLs.', $label));
        }

        return $normalized;
    }

    /**
     * @param mixed $providers
     * @return array<string, ProviderMetadata>
     */
    private function validateProviders(mixed $providers): array
    {
        if (!is_array($providers)) {
            throw new InvalidPricingCatalog('Pricing catalog providers must be defined.');
        }

        $supportedProviders = ['openai', 'anthropic', 'gemini'];
        $normalized = [];

        foreach ($supportedProviders as $providerKey) {
            $provider = $providers[$providerKey] ?? null;

            if (!is_array($provider)) {
                throw new InvalidPricingCatalog(
                    sprintf('Pricing catalog provider metadata is missing for `%s`.', $providerKey),
                );
            }

            $normalized[$providerKey] = $this->providerMetadata($providerKey, $provider);
        }

        return $normalized;
    }

    /**
     * @param array<mixed> $provider
     */
    private function providerMetadata(string $providerKey, array $provider): ProviderMetadata
    {
        $name = $provider['name'] ?? null;

        if (!is_string($name) || $name === '') {
            throw new InvalidPricingCatalog(
                sprintf('Provider `%s` must have a non-empty name.', $providerKey),
            );
        }

        $verifiedAt = $provider['verified_at'] ?? null;

        if (!is_string($verifiedAt) || !$this->isValidDateString($verifiedAt)) {
            throw new InvalidPricingCatalog(
                sprintf('Provider `%s` must have a verified_at date in YYYY-MM-DD format.', $providerKey),
            );
        }

        return new ProviderMetadata(
            name: $name,
            verifiedAt: $verifiedAt,
            sourceUrls: $this->validateSourceUrls(
                $provider['source_urls'] ?? null,
                sprintf('Provider `%s` source URLs', $providerKey),
            ),
        );
    }

    private function validateVersion(mixed $version): string
    {
        if (!is_string($version) || $version === '') {
            throw new InvalidPricingCatalog('Pricing catalog version must be a non-empty string.');
        }

        return $version;
    }

    private function isValidHttpsUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return str_starts_with($url, 'https://');
    }

    private function isValidDateString(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
