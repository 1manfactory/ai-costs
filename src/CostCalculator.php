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

        $outputCostInUsdMicros = UsdMicros::calculateAmount(
            $priceCard->outputRateInUsdMicrosPerMillionTokensFor($usage),
            $usage->outputTokens,
        );

        $totalCostInUsdMicros = $inputCostInUsdMicros + $cachedInputCostInUsdMicros + $outputCostInUsdMicros;

        foreach ($context->additionalCharges as $charge) {
            $totalCostInUsdMicros += $charge->amountInUsdMicros;
        }

        return new CostBreakdown(
            usage: $usage,
            context: $context,
            priceCard: $priceCard,
            inputCostInUsdMicros: $inputCostInUsdMicros,
            cachedInputCostInUsdMicros: $cachedInputCostInUsdMicros,
            outputCostInUsdMicros: $outputCostInUsdMicros,
            additionalCharges: $context->additionalCharges,
            totalCostInUsdMicros: $totalCostInUsdMicros,
        );
    }
}
