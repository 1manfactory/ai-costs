<?php

declare(strict_types=1);

namespace AiCosts\Contract;

use AiCosts\Value\UsageBreakdown;

interface UsageExtractorInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function extract(array $payload): UsageBreakdown;
}
