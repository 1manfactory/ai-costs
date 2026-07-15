<?php

declare(strict_types=1);

namespace AiCosts\Tests;

use AiCosts\Anthropic\AnthropicMessagesUsageExtractor;
use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\Gemini\GeminiGenerateContentUsageExtractor;
use AiCosts\OpenAI\OpenAIResponsesUsageExtractor;
use AiCosts\OpenAI\OpenAIToolPricing;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\BillingContext;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CostCalculatorTest extends TestCase
{
    public function testItCalculatesCostsFromAResponsesPayload(): void
    {
        $extractor = new OpenAIResponsesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/openai-responses.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator($this->provider());
        $breakdown = $calculator->calculate(
            $usage,
            new BillingContext(
                billingMode: BillingMode::STANDARD,
                additionalCharges: [
                    OpenAIToolPricing::webSearchCalls(3),
                ],
            ),
        );

        self::assertSame(250, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(5, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(0, $breakdown->cacheWriteInputCostInUsdMicrocent);
        self::assertSame(450, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(3705, $breakdown->totalCostInUsdMicrocent);
        self::assertSame('2026-07-15', $breakdown->pricingAsOf);
        self::assertSame('2026-07-15', $breakdown->toArray()['pricing_as_of']);
        self::assertSame(3705, $breakdown->toArray()['total_cost_in_usd_microcent']);
    }

    public function testItCalculatesCostsFromAnAnthropicMessagesPayload(): void
    {
        $extractor = new AnthropicMessagesUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/anthropic-message.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator($this->provider());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(300, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(15, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(75, $breakdown->cacheWrite5mInputCostInUsdMicrocent);
        self::assertSame(60, $breakdown->cacheWrite1hInputCostInUsdMicrocent);
        self::assertSame(600, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(1050, $breakdown->totalCostInUsdMicrocent);
    }

    public function testItCalculatesCostsFromAGeminiGenerateContentPayload(): void
    {
        $extractor = new GeminiGenerateContentUsageExtractor();
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/Fixtures/gemini-generate-content.php';
        $usage = $extractor->extract($payload);

        $calculator = new CostCalculator($this->provider());
        $breakdown = $calculator->calculate($usage);

        self::assertSame(125, $breakdown->inputCostInUsdMicrocent);
        self::assertSame(3, $breakdown->cachedInputCostInUsdMicrocent);
        self::assertSame(300, $breakdown->outputCostInUsdMicrocent);
        self::assertSame(428, $breakdown->totalCostInUsdMicrocent);
    }

    public function testCostCalculatorConstructorRemainsUnchanged(): void
    {
        $constructor = new ReflectionMethod(CostCalculator::class, '__construct');

        self::assertCount(1, $constructor->getParameters());
    }

    private function provider(): StaticPriceProvider
    {
        return StaticPriceProvider::default(new DateTimeImmutable('2026-07-15'));
    }
}
