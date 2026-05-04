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
        public int $inputCostInUsdMicros,
        public int $cachedInputCostInUsdMicros,
        public int $outputCostInUsdMicros,
        public array $additionalCharges,
        public int $totalCostInUsdMicros,
        public int $cacheWrite5mInputCostInUsdMicros = 0,
        public int $cacheWrite1hInputCostInUsdMicros = 0,
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
            'context_tier' => $this->context->serviceTier ?? $this->usage->serviceTier,
            'input_cost_in_usd_micros' => $this->inputCostInUsdMicros,
            'cached_input_cost_in_usd_micros' => $this->cachedInputCostInUsdMicros,
            'cache_write_5m_input_cost_in_usd_micros' => $this->cacheWrite5mInputCostInUsdMicros,
            'cache_write_1h_input_cost_in_usd_micros' => $this->cacheWrite1hInputCostInUsdMicros,
            'output_cost_in_usd_micros' => $this->outputCostInUsdMicros,
            'additional_charges' => array_map(
                static fn (AdditionalCharge $charge): array => [
                    'label' => $charge->label,
                    'amount_in_usd_micros' => $charge->amountInUsdMicros,
                ],
                $this->additionalCharges,
            ),
            'total_cost_in_usd_micros' => $this->totalCostInUsdMicros,
        ];
    }
}
