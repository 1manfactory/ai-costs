<?php

declare(strict_types=1);

namespace AiCosts\Value;

use AiCosts\Enum\BillingMode;
use AiCosts\Enum\ContextPricingMode;
use AiCosts\Exception\UnsupportedUsageScenario;
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
        public ?int $cacheWriteInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $cacheWrite5mInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $cacheWrite1hInputRateInUsdMicrocentPerMillionTokens = null,
        public ?int $longContextCacheWriteInputRateInUsdMicrocentPerMillionTokens = null,
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
            longContextThresholdTokens: self::nullableInt($card, 'long_context_threshold_input_tokens'),
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
            cacheWriteInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cache_write_input_usd_microcent_per_million_tokens',
            ),
            cacheWrite5mInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cache_write_5m_input_usd_microcent_per_million_tokens',
            ),
            cacheWrite1hInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'cache_write_1h_input_usd_microcent_per_million_tokens',
            ),
            longContextCacheWriteInputRateInUsdMicrocentPerMillionTokens: self::nullableInt(
                $card,
                'long_context_cache_write_input_usd_microcent_per_million_tokens',
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

    public function usesLongContext(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): bool {
        return match ($contextPricingMode) {
            ContextPricingMode::SHORT => false,
            ContextPricingMode::LONG => true,
            ContextPricingMode::AUTO => $this->longContextThresholdTokens !== null
                && $usage->inputTokens > $this->longContextThresholdTokens,
        };
    }

    public function inputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): int {
        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $this->inputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->requiredLongContextRate(
            $this->longContextInputRateInUsdMicrocentPerMillionTokens,
            'input',
        );
    }

    public function cachedInputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): int {
        $defaultRate = $this->cachedInputRateInUsdMicrocentPerMillionTokens
            ?? $this->inputRateInUsdMicrocentPerMillionTokens;

        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $defaultRate;
        }

        if ($this->longContextCachedInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextCachedInputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->requiredLongContextRate(
            $this->longContextInputRateInUsdMicrocentPerMillionTokens,
            'cached input',
        );
    }

    public function outputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): int {
        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $this->outputRateInUsdMicrocentPerMillionTokens;
        }

        return $this->requiredLongContextRate(
            $this->longContextOutputRateInUsdMicrocentPerMillionTokens,
            'output',
        );
    }

    public function cacheWriteInputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): ?int {
        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $this->cacheWriteInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($this->longContextCacheWriteInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextCacheWriteInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($contextPricingMode === ContextPricingMode::LONG && $usage->cacheWriteInputTokens > 0) {
            throw $this->missingLongContextRateException('generic cache-write input');
        }

        return $this->cacheWriteInputRateInUsdMicrocentPerMillionTokens;
    }

    public function cacheWrite5mInputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): ?int {
        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $this->cacheWrite5mInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($this->longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextCacheWrite5mInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($contextPricingMode === ContextPricingMode::LONG && $usage->cacheWrite5mInputTokens > 0) {
            throw $this->missingLongContextRateException('5-minute cache-write input');
        }

        return $this->cacheWrite5mInputRateInUsdMicrocentPerMillionTokens;
    }

    public function cacheWrite1hInputRateInUsdMicrocentPerMillionTokensFor(
        UsageBreakdown $usage,
        ContextPricingMode $contextPricingMode = ContextPricingMode::AUTO,
    ): ?int {
        if (!$this->usesLongContext($usage, $contextPricingMode)) {
            return $this->cacheWrite1hInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($this->longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens !== null) {
            return $this->longContextCacheWrite1hInputRateInUsdMicrocentPerMillionTokens;
        }

        if ($contextPricingMode === ContextPricingMode::LONG && $usage->cacheWrite1hInputTokens > 0) {
            throw $this->missingLongContextRateException('1-hour cache-write input');
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

    private function requiredLongContextRate(?int $rate, string $rateName): int
    {
        if ($rate !== null) {
            return $rate;
        }

        throw $this->missingLongContextRateException($rateName);
    }

    private function missingLongContextRateException(string $rateName): UnsupportedUsageScenario
    {
        return new UnsupportedUsageScenario(
            sprintf(
                'Long-context %s pricing is not configured for model `%s` in `%s` billing mode.',
                $rateName,
                $this->model,
                $this->billingMode->value,
            ),
        );
    }
}
