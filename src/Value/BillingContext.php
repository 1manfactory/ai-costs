<?php

declare(strict_types=1);

namespace AiCosts\Value;

use AiCosts\Enum\BillingMode;
use InvalidArgumentException;

final readonly class BillingContext
{
    /**
     * @var list<AdditionalCharge>
     */
    public array $additionalCharges;

    /**
     * @param list<mixed> $additionalCharges
     */
    public function __construct(
        public BillingMode $billingMode = BillingMode::STANDARD,
        public ?string $serviceTier = null,
        array $additionalCharges = [],
    ) {
        $validatedCharges = [];

        foreach ($additionalCharges as $charge) {
            if (!$charge instanceof AdditionalCharge) {
                throw new InvalidArgumentException(
                    'Expected all additional charges to be instances of AdditionalCharge.',
                );
            }

            $validatedCharges[] = $charge;
        }

        $this->additionalCharges = $validatedCharges;
    }
}
