<?php

declare(strict_types=1);

namespace AiCosts\OpenAI;

use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\Support\UsdMicrocent;
use AiCosts\Value\AdditionalCharge;

final class OpenAIToolPricing
{
    public const AS_OF = '2026-05-02';

    public static function webSearchCalls(int $calls): AdditionalCharge
    {
        self::assertPositive($calls, 'calls');

        return new AdditionalCharge(
            label: sprintf('OpenAI web search (%d calls)', $calls),
            amountInUsdMicrocent: UsdMicrocent::calculateAmount(1_000_000, $calls, 1000),
        );
    }

    public static function webSearchPreviewCalls(int $calls, bool $nonReasoningModel = false): AdditionalCharge
    {
        self::assertPositive($calls, 'calls');

        $label = $nonReasoningModel
            ? sprintf('OpenAI web search preview (%d non-reasoning calls)', $calls)
            : sprintf('OpenAI web search preview (%d reasoning calls)', $calls);

        return new AdditionalCharge(
            label: $label,
            amountInUsdMicrocent: UsdMicrocent::calculateAmount(
                $nonReasoningModel ? 2_500_000 : 1_000_000,
                $calls,
                1000,
            ),
        );
    }

    public static function fileSearchCalls(int $calls): AdditionalCharge
    {
        self::assertPositive($calls, 'calls');

        return new AdditionalCharge(
            label: sprintf('OpenAI file search (%d calls)', $calls),
            amountInUsdMicrocent: UsdMicrocent::calculateAmount(250_000, $calls, 1000),
        );
    }

    public static function containerSession(int $memoryGb): AdditionalCharge
    {
        $priceMap = [
            1 => 3_000,
            4 => 12_000,
            16 => 48_000,
            64 => 192_000,
        ];

        if (!isset($priceMap[$memoryGb])) {
            throw new InvalidUsagePayload('Container sessions currently support 1, 4, 16, or 64 GB.');
        }

        return new AdditionalCharge(
            label: sprintf('OpenAI container session (%d GB / 20 min)', $memoryGb),
            amountInUsdMicrocent: $priceMap[$memoryGb],
        );
    }

    private static function assertPositive(int $value, string $name): void
    {
        if ($value < 1) {
            throw new InvalidUsagePayload(sprintf('Expected `%s` to be >= 1.', $name));
        }
    }
}
