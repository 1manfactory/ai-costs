<?php

declare(strict_types=1);

namespace AiCosts\Enum;

enum BillingMode: string
{
    case STANDARD = 'standard';
    case BATCH = 'batch';
    case FLEX = 'flex';
    case PRIORITY = 'priority';
}
