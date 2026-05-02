<?php

declare(strict_types=1);

namespace AiCosts\Value;

use AiCosts\Enum\BillingMode;
use InvalidArgumentException;

final readonly class PriceCard
{
    public function __construct(
        public string $model,
        public BillingMode $billingMode,
        public int $inputRateInUsdMicrosPerMillionTokens,
        public ?int $cachedInputRateInUsdMicrosPerMillionTokens,
        public int $outputRateInUsdMicrosPerMillionTokens,
        public ?int $longContextThresholdTokens = null,
        public ?int $longContextInputRateInUsdMicrosPerMillionTokens = null,
        public ?int $longContextCachedInputRateInUsdMicrosPerMillionTokens = null,
        public ?int $longContextOutputRateInUsdMicrosPerMillionTokens = null,
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
            inputRateInUsdMicrosPerMillionTokens: self::requiredInt($card, 'input_usd_micros_per_million_tokens'),
            cachedInputRateInUsdMicrosPerMillionTokens: self::nullableInt(
                $card,
                'cached_input_usd_micros_per_million_tokens',
            ),
            outputRateInUsdMicrosPerMillionTokens: self::requiredInt($card, 'output_usd_micros_per_million_tokens'),
            longContextThresholdTokens: self::nullableInt(
                $card,
                'long_context_threshold_input_tokens',
            ),
            longContextInputRateInUsdMicrosPerMillionTokens: self::nullableInt(
                $card,
                'long_context_input_usd_micros_per_million_tokens',
            ),
            longContextCachedInputRateInUsdMicrosPerMillionTokens: self::nullableInt(
                $card,
                'long_context_cached_input_usd_micros_per_million_tokens',
            ),
            longContextOutputRateInUsdMicrosPerMillionTokens: self::nullableInt(
                $card,
                'long_context_output_usd_micros_per_million_tokens',
            ),
        );
    }

    public function usesLongContext(UsageBreakdown $usage): bool
    {
        return $this->longContextThresholdTokens !== null
            && $usage->inputTokens > $this->longContextThresholdTokens;
    }

    public function inputRateInUsdMicrosPerMillionTokensFor(UsageBreakdown $usage): int
    {
        if ($this->usesLongContext($usage) && $this->longContextInputRateInUsdMicrosPerMillionTokens !== null) {
            return $this->longContextInputRateInUsdMicrosPerMillionTokens;
        }

        return $this->inputRateInUsdMicrosPerMillionTokens;
    }

    public function cachedInputRateInUsdMicrosPerMillionTokensFor(UsageBreakdown $usage): int
    {
        $defaultRate = $this->cachedInputRateInUsdMicrosPerMillionTokens
            ?? $this->inputRateInUsdMicrosPerMillionTokens;

        if (!$this->usesLongContext($usage)) {
            return $defaultRate;
        }

        if ($this->longContextCachedInputRateInUsdMicrosPerMillionTokens !== null) {
            return $this->longContextCachedInputRateInUsdMicrosPerMillionTokens;
        }

        if ($this->longContextInputRateInUsdMicrosPerMillionTokens !== null) {
            return $this->longContextInputRateInUsdMicrosPerMillionTokens;
        }

        return $defaultRate;
    }

    public function outputRateInUsdMicrosPerMillionTokensFor(UsageBreakdown $usage): int
    {
        if ($this->usesLongContext($usage) && $this->longContextOutputRateInUsdMicrosPerMillionTokens !== null) {
            return $this->longContextOutputRateInUsdMicrosPerMillionTokens;
        }

        return $this->outputRateInUsdMicrosPerMillionTokens;
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
