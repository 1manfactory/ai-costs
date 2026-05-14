<?php

declare(strict_types=1);

namespace AiCosts\Value;

use AiCosts\Enum\BillingMode;
use InvalidArgumentException;

/**
 * @SuppressWarnings("PHPMD.ExcessiveParameterList")
 */
final readonly class PriceCard
{
    public function __construct(
        public string $model,
        public BillingMode $billingMode,
        public int $inputRateInUsdMicrocentPerMillionTokens,
        public ?int $cachedInputRateInUsdMicrocentPerMillionTokens,
        public int $outputRateInUsdMicrocentPerMillionTokens,
        public ?int $longContextThresholdTokens = null,
        public ?int $longContextInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $longContextCachedInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $longContextOutputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $cacheWrite5mInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $cacheWrite1hInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens = null,
    ) {
    }

    /**
     * @param array<string, mixed> $card
     */
    public static function fromArray(string $model, BillingMode $billingMode, array $card): self
    {
        return new self(
            model: $model,
            billingMode: $billingMode,
            inputRateInUsdMicrocentPerMillionTokens: self::requiredInt($card, 'input_usd_microcent_per_million_tokens'),
            cachedInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cached_input_usd_microcent_per_million_tokens',
            ),
            outputRateInUsdMicrocentPerMillionTokens: self::requiredInt(
                $card,
                'output_usd_microcent_per_million_tokens',
            ),
            longContextThresholdTokens: self::nullableInt(
                $card,
                'long_context_threshold_input_tokens',
            ),
            longContextInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_input_usd_microcent_per_million_tokens',
            ),
            longContextCachedInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_cached_input_usd_microcent_per_million_tokens',
            ),
            longContextOutputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_output_usd_microcent_per_million_tokens',
            ),
            cacheWrite5mInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cache_write_5m_input_usd_microcent_per_million_tokens',
            ),
            cacheWrite1hInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cache_write_1h_input_usd_microcent_per_million_tokens',
            ),
            longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_cache_write_5m_input_usd_microcent_per_million_tokens',
            ),
            longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_cache_write_1h_input_usd_microcent_per_million_tokens',
            ),
        );
    }

    public function usesLongContext(UsageBreakdown $usage): bool
    {
        return $this->longContextThresholdTokens !== null
            && $usage->inputTokens > $this->longContextThresholdTokens;
    }

    public function inputRateInUsdMicrocentPerMillionTokensFor(UsageBreakdown $usage): int
    {
        if ($this->usesLongContext($usage) && $this->longContextInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextInputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->inputRateInUsdMicrocentPerMillionTokens;
    }

    public function cachedInputRateInUsdMicrocentPerMillionTokensFor(UsageBreakdown $usage): int
    {
        $defaultRate = $this->cachedInputRateInUsdMicrocentPerMillionTokens
            ?? $this->inputRateInUsdMicrocentPerMillionTokens;

        if (!$this->usesLongContext($usage)) {
            return $defaultRate;
        }

        if ($this->longContextCachedInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextCachedInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($this->longContextInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextInputRateInUsdMicrocentPerMillionTokens;
        }

        return $defaultRate;
    }

    public function outputRateInUsdMicrocentPerMillionTokensFor(UsageBreakdown $usage): int
    {
        if ($this->usesLongContext($usage) && $this->longContextOutputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextOutputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->outputRateInUsdMicrocentPerMillionTokens;
    }

    public function cacheWrite5mInputRateInUsdMicrocentPerMillionTokensFor(UsageBreakdown $usage): ?int
    {
        if (
            $this->usesLongContext($usage)
            && $this->longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens !== null
        ) {
            return $this->longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->cacheWrite5mInputRateInUsdMicrocentPerMillionTokens;
    }

    public function cacheWrite1hInputRateInUsdMicrocentPerMillionTokensFor(UsageBreakdown $usage): ?int
    {
        if (
            $this->usesLongContext($usage)
            && $this->longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens !== null
        ) {
            return $this->longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->cacheWrite1hInputRateInUsdMicrocentPerMillionTokens;
    }

    /**
     * @param array<string, mixed> $card
     */
    private static function requiredInt(array $card, string $key): int
    {
        $value = $card[$key] ?? null;

        if (!is_int($value)) {
            throw new InvalidArgumentException(sprintf('Expected `%s` to be an integer.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $card
     */
    private static function nullableInt(array $card, string $key): ?int
    {
        $value = $card[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_int($value)) {
            throw new InvalidArgumentException(sprintf('Expected `%s` to be an integer.', $key));
        }

        return $value;
    }
}
