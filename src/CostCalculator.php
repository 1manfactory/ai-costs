<?php

declare(strict_types=1);

namespace AiCosts;

use AiCosts\Contract\PriceProviderInterface;
use AiCosts\Exception\UnsupportedUsageScenario;
use AiCosts\Support\UsdMicros;
use AiCosts\Value\BillingContext;
use AiCosts\Value\CostBreakdown;
use AiCosts\Value\UsageBreakdown;

final readonly class CostCalculator
{
    public function __construct(
        private PriceProviderInterface $priceProvider,
    ) {
    }

    public function calculate(UsageBreakdown $usage, ?BillingContext $context = null): CostBreakdown
    {
        if ($usage->inputAudioTokens > 0 || $usage->outputAudioTokens > 0) {
            throw new UnsupportedUsageScenario('Audio token pricing is not implemented yet in this package.');
        }

        $context ??= new BillingContext();
        $priceCard = $this->priceProvider->get($usage->model, $context->billingMode);

        $inputCostInUsdMicros = UsdMicros::calculateAmount(
            $priceCard->inputRateInUsdMicrosPerMillionTokensFor($usage),
            $usage->uncachedInputTokens(),
        );

        $cachedInputCostInUsdMicros = UsdMicros::calculateAmount(
            $priceCard->cachedInputRateInUsdMicrosPerMillionTokensFor($usage),
            $usage->cachedInputTokens,
        );

        $cacheWrite5mInputRate = $priceCard->cacheWrite5mInputRateInUsdMicrosPerMillionTokensFor($usage);
        $cacheWrite1hInputRate = $priceCard->cacheWrite1hInputRateInUsdMicrosPerMillionTokensFor($usage);

        if ($usage->cacheWrite5mInputTokens > 0 && $cacheWrite5mInputRate === null) {
            throw new UnsupportedUsageScenario('5-minute cache-write pricing is not configured for this model.');
        }

        if ($usage->cacheWrite1hInputTokens > 0 && $cacheWrite1hInputRate === null) {
            throw new UnsupportedUsageScenario('1-hour cache-write pricing is not configured for this model.');
        }

        $cacheWrite5mInputCostInUsdMicros = UsdMicros::calculateAmount(
            $cacheWrite5mInputRate ?? 0,
            $usage->cacheWrite5mInputTokens,
        );

        $cacheWrite1hInputCostInUsdMicros = UsdMicros::calculateAmount(
            $cacheWrite1hInputRate ?? 0,
            $usage->cacheWrite1hInputTokens,
        );

        $outputCostInUsdMicros = UsdMicros::calculateAmount(
            $priceCard->outputRateInUsdMicrosPerMillionTokensFor($usage),
            $usage->outputTokens,
        );

        $totalCostInUsdMicros = $inputCostInUsdMicros
            + $cachedInputCostInUsdMicros
            + $cacheWrite5mInputCostInUsdMicros
            + $cacheWrite1hInputCostInUsdMicros
            + $outputCostInUsdMicros;

        foreach ($context->additionalCharges as $charge) {
            $totalCostInUsdMicros += $charge->amountInUsdMicros;
        }

        return new CostBreakdown(
            usage: $usage,
            context: $context,
            priceCard: $priceCard,
            pricingAsOf: $this->priceProvider->asOf(),
            inputCostInUsdMicros: $inputCostInUsdMicros,
            cachedInputCostInUsdMicros: $cachedInputCostInUsdMicros,
            outputCostInUsdMicros: $outputCostInUsdMicros,
            additionalCharges: $context->additionalCharges,
            totalCostInUsdMicros: $totalCostInUsdMicros,
            cacheWrite5mInputCostInUsdMicros: $cacheWrite5mInputCostInUsdMicros,
            cacheWrite1hInputCostInUsdMicros: $cacheWrite1hInputCostInUsdMicros,
        );
    }
}
