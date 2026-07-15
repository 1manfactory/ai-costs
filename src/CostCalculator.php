<?php

declare(strict_types=1);

namespace AiCosts;

use AiCosts\Contract\PriceProviderInterface;
use AiCosts\Exception\UnsupportedUsageScenario;
use AiCosts\Support\UsdMicrocent;
use AiCosts\Value\BillingContext;
use AiCosts\Value\CostBreakdown;
use AiCosts\Value\PriceCard;
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

        $inputCostInUsdMicrocent = $this->inputCost($usage, $context, $priceCard);
        $cachedInputCostInUsdMicrocent = $this->cachedInputCost($usage, $context, $priceCard);
        $cacheWriteCosts = $this->cacheWriteCosts($usage, $context, $priceCard);
        $outputCostInUsdMicrocent = $this->outputCost($usage, $context, $priceCard);

        $totalCostInUsdMicrocent = $inputCostInUsdMicrocent
            + $cachedInputCostInUsdMicrocent
            + $cacheWriteCosts['generic']
            + $cacheWriteCosts['five_minute']
            + $cacheWriteCosts['one_hour']
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
            cacheWriteInputCostInUsdMicrocent: $cacheWriteCosts['generic'],
            cacheWrite5mInputCostInUsdMicrocent: $cacheWriteCosts['five_minute'],
            cacheWrite1hInputCostInUsdMicrocent: $cacheWriteCosts['one_hour'],
        );
    }

    private function inputCost(UsageBreakdown $usage, BillingContext $context, PriceCard $priceCard): int
    {
        return UsdMicrocent::calculateAmount(
            $priceCard->inputRateInUsdMicrocentPerMillionTokensFor($usage, $context->contextPricingMode),
            $usage->uncachedInputTokens(),
        );
    }

    private function cachedInputCost(UsageBreakdown $usage, BillingContext $context, PriceCard $priceCard): int
    {
        return UsdMicrocent::calculateAmount(
            $priceCard->cachedInputRateInUsdMicrocentPerMillionTokensFor($usage, $context->contextPricingMode),
            $usage->cachedInputTokens,
        );
    }

    /**
     * @return array{generic: int, five_minute: int, one_hour: int}
     */
    private function cacheWriteCosts(UsageBreakdown $usage, BillingContext $context, PriceCard $priceCard): array
    {
        $genericRate = $priceCard->cacheWriteInputRateInUsdMicrocentPerMillionTokensFor(
            $usage,
            $context->contextPricingMode,
        );
        $fiveMinuteRate = $priceCard->cacheWrite5mInputRateInUsdMicrocentPerMillionTokensFor(
            $usage,
            $context->contextPricingMode,
        );
        $oneHourRate = $priceCard->cacheWrite1hInputRateInUsdMicrocentPerMillionTokensFor(
            $usage,
            $context->contextPricingMode,
        );

        $this->assertCacheWriteRate($usage->cacheWriteInputTokens, $genericRate, $priceCard, 'Generic');
        $this->assertCacheWriteRate($usage->cacheWrite5mInputTokens, $fiveMinuteRate, $priceCard, '5-minute');
        $this->assertCacheWriteRate($usage->cacheWrite1hInputTokens, $oneHourRate, $priceCard, '1-hour');

        return [
            'generic' => UsdMicrocent::calculateAmount($genericRate ?? 0, $usage->cacheWriteInputTokens),
            'five_minute' => UsdMicrocent::calculateAmount($fiveMinuteRate ?? 0, $usage->cacheWrite5mInputTokens),
            'one_hour' => UsdMicrocent::calculateAmount($oneHourRate ?? 0, $usage->cacheWrite1hInputTokens),
        ];
    }

    private function outputCost(UsageBreakdown $usage, BillingContext $context, PriceCard $priceCard): int
    {
        return UsdMicrocent::calculateAmount(
            $priceCard->outputRateInUsdMicrocentPerMillionTokensFor($usage, $context->contextPricingMode),
            $usage->outputTokens,
        );
    }

    private function assertCacheWriteRate(
        int $tokenCount,
        ?int $rate,
        PriceCard $priceCard,
        string $label,
    ): void {
        if ($tokenCount === 0 || $rate !== null) {
            return;
        }

        throw new UnsupportedUsageScenario(
            sprintf(
                '%s cache-write pricing is not configured for model `%s` in `%s` billing mode.',
                $label,
                $priceCard->model,
                $priceCard->billingMode->value,
            ),
        );
    }
}
