<?php

declare(strict_types=1);

namespace AiCosts\Support;

use InvalidArgumentException;

/**
 * Project money unit: one hundred-thousandth of one US dollar.
 *
 * 1 USD = 100,000 usd_microcent
 * 1 usd_microcent = 0.00001 USD = 0.001 cents
 */
final class UsdMicrocent
{
    public static function calculateAmount(
        int $rateInUsdMicrocentPerUnitBlock,
        int $billedUnits,
        int $unitBlockSize = 1_000_000,
    ): int {
        if ($billedUnits < 0) {
            throw new InvalidArgumentException('billedUnits must be >= 0.');
        }

        if ($unitBlockSize < 1) {
            throw new InvalidArgumentException('unitBlockSize must be >= 1.');
        }

        return self::roundDivide($rateInUsdMicrocentPerUnitBlock * $billedUnits, $unitBlockSize);
    }

    private static function roundDivide(int $numerator, int $denominator): int
    {
        $half = intdiv($denominator, 2);

        if ($numerator >= 0) {
            return intdiv($numerator + $half, $denominator);
        }

        return -intdiv(abs($numerator) + $half, $denominator);
    }
}
