<?php

declare(strict_types=1);

namespace AiCosts\Value;

final readonly class ProviderMetadata
{
    /**
     * @param list<string> $sourceUrls
     */
    public function __construct(
        public string $name,
        public string $verifiedAt,
        public array $sourceUrls,
    ) {
    }
}
