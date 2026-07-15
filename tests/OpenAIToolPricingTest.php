<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\OpenAI\OpenAIToolPricing;
use PHPUnit\Framework\TestCase;

final class OpenAIToolPricingTest extends TestCase
{
    public function testFixedContainerSessionPricingRemainsUnchanged(): void
    {
        self::assertSame(3_000, OpenAIToolPricing::containerSession(1)->amountInUsdMicrocent);
        self::assertSame(12_000, OpenAIToolPricing::containerSession(4)->amountInUsdMicrocent);
        self::assertSame(48_000, OpenAIToolPricing::containerSession(16)->amountInUsdMicrocent);
        self::assertSame(192_000, OpenAIToolPricing::containerSession(64)->amountInUsdMicrocent);
    }

    public function testOneMinuteContainerSessionBillsFiveMinutes(): void
    {
        $charge = OpenAIToolPricing::containerSessionMinutes(memoryGb: 1, durationMinutes: 1);

        self::assertSame(750, $charge->amountInUsdMicrocent);
        self::assertStringContainsString('1 actual min / 5 billed min', $charge->label);
    }

    public function testFiveMinuteContainerSessionBillsFiveMinutes(): void
    {
        $charge = OpenAIToolPricing::containerSessionMinutes(memoryGb: 1, durationMinutes: 5);

        self::assertSame(750, $charge->amountInUsdMicrocent);
    }

    public function testSixMinuteContainerSessionBillsSixMinutes(): void
    {
        $charge = OpenAIToolPricing::containerSessionMinutes(memoryGb: 4, durationMinutes: 6);

        self::assertSame(3_600, $charge->amountInUsdMicrocent);
    }

    public function testTwentyMinuteContainerSessionMatchesTheFixedPrice(): void
    {
        self::assertSame(
            OpenAIToolPricing::containerSession(16)->amountInUsdMicrocent,
            OpenAIToolPricing::containerSessionMinutes(memoryGb: 16, durationMinutes: 20)->amountInUsdMicrocent,
        );
    }

    public function testItRejectsAnUnsupportedContainerMemorySize(): void
    {
        $this->expectException(InvalidUsagePayload::class);
        $this->expectExceptionMessage('Container sessions currently support 1, 4, 16, or 64 GB.');

        OpenAIToolPricing::containerSessionMinutes(memoryGb: 2, durationMinutes: 5);
    }

    public function testItRejectsZeroOrNegativeDuration(): void
    {
        $this->expectException(InvalidUsagePayload::class);
        $this->expectExceptionMessage('Expected `durationMinutes` to be >= 1.');

        OpenAIToolPricing::containerSessionMinutes(memoryGb: 4, durationMinutes: 0);
    }

    public function testItRejectsNegativeDuration(): void
    {
        $this->expectException(InvalidUsagePayload::class);
        $this->expectExceptionMessage('Expected `durationMinutes` to be >= 1.');

        OpenAIToolPricing::containerSessionMinutes(memoryGb: 4, durationMinutes: -1);
    }
}
