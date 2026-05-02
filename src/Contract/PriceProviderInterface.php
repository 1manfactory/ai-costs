<?php

declare(strict_types=1);

namespace AiCosts\Contract;

use AiCosts\Enum\BillingMode;
use AiCosts\Value\PriceCard;

interface PriceProviderInterface
{
    public function asOf(): string;

    public function get(string $model, BillingMode $billingMode): PriceCard;

    /**
     * @return list<string>
     */
    public function pricedModels(): array;
}
