<?php

declare(strict_types=1);

namespace AiCosts;

use AiCosts\Contract\PriceProviderInterface;
use AiCosts\Exception\UnsupportedUsageScenario;
use AiCosts\Support\UsdMicrocent;
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

        $inputCostInUsdMicrocent = UsdMicrocent::calculateAmount(
            $priceCard->inputRateInUsdMicrocentPerMillionTokensFor($usage),
            $usage->uncachedInputTokens(),
        );

        $cachedInputCostInUsdMicrocent = UsdMicrocent::calculateAmount(
            $priceCard->cachedInputRateInUsdMicrocentPerMillionTokensFor($usage),
            $usage->cachedInputTokens,
        );

        $cacheWrite5mInputRate = $priceCard->cacheWrite5mInputRateInUsdMicrocentPerMillionTokensFor($usage);
        $cacheWrite1hInputRate = $priceCard->cacheWrite1hInputRateInUsdMicrocentPerMillionTokensFor($usage);

        if ($usage->cacheWrite5mInputTokens > 0 && $cacheWrite5mInputRate === null) {
            throw new UnsupportedUsageScenario('5-minute cache-write pricing is not configured for this model.');
        }

        if ($usage->cacheWrite1hInputTokens > 0 && $cacheWrite1hInputRate === null) {
            throw new UnsupportedUsageScenario('1-hour cache-write pricing is not configured for this model.');
        }

        $cacheWrite5mInputCostInUsdMicrocent = UsdMicrocent::calculateAmount(
            $cacheWrite5mInputRate ?? 0,
            $usage->cacheWrite5mInputTokens,
        );

        $cacheWrite1hInputCostInUsdMicrocent = UsdMicrocent::calculateAmount(
            $cacheWrite1hInputRate ?? 0,
            $usage->cacheWrite1hInputTokens,
        );

        $outputCostInUsdMicrocent = UsdMicrocent::calculateAmount(
            $priceCard->outputRateInUsdMicrocentPerMillionTokensFor($usage),
            $usage->outputTokens,
        );

        $totalCostInUsdMicrocent = $inputCostInUsdMicrocent
            + $cachedInputCostInUsdMicrocent
            + $cacheWrite5mInputCostInUsdMicrocent
            + $cacheWrite1hInputCostInUsdMicrocent
            + $outputCostInUsdMicrocent;

        foreach ($context->additionalCharges as $charge) {
            $totalCostInUsdMicrocent += $charge->amountInUsdMicrocent;
        }

        return new CostBreakdown(
            usage: $usage,
            context: $context,
            priceCard: $priceCard,
            pricingAsOf: $this->priceProvider->asOf(),
            inputCostInUsdMicrocent: $inputCostInUsdMicrocent,
            cachedInputCostInUsdMicrocent: $cachedInputCostInUsdMicrocent,
            outputCostInUsdMicrocent: $outputCostInUsdMicrocent,
            additionalCharges: $context->additionalCharges,
            totalCostInUsdMicrocent: $totalCostInUsdMicrocent,
            cacheWrite5mInputCostInUsdMicrocent: $cacheWrite5mInputCostInUsdMicrocent,
            cacheWrite1hInputCostInUsdMicrocent: $cacheWrite1hInputCostInUsdMicrocent,
        );
    }
}
