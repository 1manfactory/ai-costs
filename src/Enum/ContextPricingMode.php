<?php

declare(strict_types=1);

namespace AiCosts\Enum;

enum ContextPricingMode: string
{
    case AUTO = 'auto';
    case SHORT = 'short';
    case LONG = 'long';
}
