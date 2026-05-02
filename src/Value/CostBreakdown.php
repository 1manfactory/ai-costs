<?php

declare(strict_types=1);

namespace AiCosts\Value;

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
            'context_tier' => $this->context->serviceTier,
            'input_cost_in_usd_micros' => $this->inputCostInUsdMicros,
            'cached_input_cost_in_usd_micros' => $this->cachedInputCostInUsdMicros,
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
