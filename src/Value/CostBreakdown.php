<?php

declare(strict_types=1);

namespace AiCosts\Value;

/**
 * @SuppressWarnings("PHPMD.ExcessiveParameterList")
 */
final readonly class CostBreakdown
{
    /**
     * @param list<AdditionalCharge> $additionalCharges
     */
    public function __construct(
        public UsageBreakdown $usage,
        public BillingContext $context,
        public PriceCard $priceCard,
        public string $pricingAsOf,
        public int $inputCostInUsdMicrocent,
        public int $cachedInputCostInUsdMicrocent,
        public int $outputCostInUsdMicrocent,
        public array $additionalCharges,
        public int $totalCostInUsdMicrocent,
        public int $cacheWrite5mInputCostInUsdMicrocent = 0,
        public int $cacheWrite1hInputCostInUsdMicrocent = 0,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'model' => $this->usage->model,
            'source' => $this->usage->source,
            'billing_mode' => $this->context->billingMode->value,
            'pricing_as_of' => $this->pricingAsOf,
            'context_tier' => $this->context->serviceTier ?? $this->usage->serviceTier,
            'input_cost_in_usd_microcent' => $this->inputCostInUsdMicrocent,
            'cached_input_cost_in_usd_microcent' => $this->cachedInputCostInUsdMicrocent,
            'cache_write_5m_input_cost_in_usd_microcent' => $this->cacheWrite5mInputCostInUsdMicrocent,
            'cache_write_1h_input_cost_in_usd_microcent' => $this->cacheWrite1hInputCostInUsdMicrocent,
            'output_cost_in_usd_microcent' => $this->outputCostInUsdMicrocent,
            'additional_charges' => array_map(
                static fn (AdditionalCharge $charge): array => [
                    'label' => $charge->label,
                    'amount_in_usd_microcent' => $charge->amountInUsdMicrocent,
                ],
                $this->additionalCharges,
            ),
            'total_cost_in_usd_microcent' => $this->totalCostInUsdMicrocent,
        ];
    }
}
