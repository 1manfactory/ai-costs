<?php

declare(strict_types=1);

namespace AiCosts\Value;

final readonly class AdditionalCharge
{
    public function __construct(
        public string $label,
        public int $amountInUsdMicrocent,
    ) {
    }
}
