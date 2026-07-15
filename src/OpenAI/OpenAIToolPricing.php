<?php

declare(strict_types=1);

namespace AiCosts\OpenAI;

use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\Support\UsdMicrocent;
use AiCosts\Value\AdditionalCharge;

final class OpenAIToolPricing
{
    public const AS_OF = '2026-07-15';

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
        $priceMap = self::containerSessionPriceMap();
        self::assertSupportedContainerMemory($memoryGb, array_keys($priceMap));

        return new AdditionalCharge(
            label: sprintf('OpenAI container session (%d GB / fixed 20-minute session)', $memoryGb),
            amountInUsdMicrocent: $priceMap[$memoryGb],
        );
    }

    public static function containerSessionMinutes(int $memoryGb, int $durationMinutes): AdditionalCharge
    {
        $perMinutePriceMap = [
            1 => 150,
            4 => 600,
            16 => 2_400,
            64 => 9_600,
        ];

        self::assertSupportedContainerMemory($memoryGb, array_keys($perMinutePriceMap));
        self::assertPositive($durationMinutes, 'durationMinutes');

        $billableMinutes = max(5, $durationMinutes);

        return new AdditionalCharge(
            label: sprintf(
                'OpenAI container session (%d GB / %d actual min / %d billed min)',
                $memoryGb,
                $durationMinutes,
                $billableMinutes,
            ),
            amountInUsdMicrocent: $perMinutePriceMap[$memoryGb] * $billableMinutes,
        );
    }

    private static function assertPositive(int $value, string $name): void
    {
        if ($value < 1) {
            throw new InvalidUsagePayload(sprintf('Expected `%s` to be >= 1.', $name));
        }
    }

    /**
     * @param list<int> $supportedMemorySizes
     */
    private static function assertSupportedContainerMemory(int $memoryGb, array $supportedMemorySizes): void
    {
        if (!in_array($memoryGb, $supportedMemorySizes, true)) {
            throw new InvalidUsagePayload('Container sessions currently support 1, 4, 16, or 64 GB.');
        }
    }

    /**
     * @return array<int, int>
     */
    private static function containerSessionPriceMap(): array
    {
        return [
            1 => 3_000,
            4 => 12_000,
            16 => 48_000,
            64 => 192_000,
        ];
    }
}
