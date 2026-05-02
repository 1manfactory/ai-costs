<?php

declare(strict_types=1);

namespace AiCosts\Support;

use InvalidArgumentException;

final class UsdMicros
{
    public static function calculateAmount(
        int $rateInUsdMicrosPerUnitBlock,
        int $billedUnits,
        int $unitBlockSize = 1_000_000,
    ): int {
        if ($billedUnits < 0) {
            throw new InvalidArgumentException('billedUnits must be >= 0.');
        }

        if ($unitBlockSize < 1) {
            throw new InvalidArgumentException('unitBlockSize must be >= 1.');
        }

        return self::roundDivide($rateInUsdMicrosPerUnitBlock * $billedUnits, $unitBlockSize);
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
