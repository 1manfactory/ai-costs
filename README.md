# ai-costs

`ai-costs` is an unofficial PHP library for calculating OpenAI API costs from usage payloads.

It is designed to stay SDK-agnostic: feed it an OpenAI Responses payload, a Chat Completions payload, or a normalized usage object, and it returns a detailed cost breakdown.

> [!WARNING]
> This library provides estimates only. I do not guarantee the accuracy, completeness, or fitness of the calculated values for any billing purpose. The amounts actually billed by OpenAI are authoritative and always take precedence.

## Units

All calculated cost values are returned as integers in `usd_micros`.

- `1 USD = 1_000_000 usd_micros`
- Public variable and method names use explicit unit suffixes like `InUsdMicros`
- The package returns integers only

## Current scope

- OpenAI `Responses API` usage extraction
- OpenAI `Chat Completions API` usage extraction
- Text token pricing for selected GPT-5.4 and GPT-5.5 family models
- Automatic long-context pricing for `gpt-5.5`, `gpt-5.4`, and `gpt-5.4-pro`
- Helper charges for web search, file search, and container sessions

## Installation

```bash
composer require 1manfactory/ai-costs
```

## Quick start

```php
<?php

declare(strict_types=1);

use AiCosts\CostCalculator;
use AiCosts\Enum\BillingMode;
use AiCosts\OpenAI\OpenAIResponsesUsageExtractor;
use AiCosts\OpenAI\OpenAIToolPricing;
use AiCosts\Pricing\StaticPriceProvider;
use AiCosts\Value\BillingContext;

$payload = [
    'model' => 'gpt-5.4',
    'usage' => [
        'input_tokens' => 1200,
        'input_tokens_details' => ['cached_tokens' => 200],
        'output_tokens' => 300,
        'output_tokens_details' => ['reasoning_tokens' => 50],
    ],
];

$extractor = new OpenAIResponsesUsageExtractor();
$usage = $extractor->extract($payload);

$calculator = new CostCalculator(StaticPriceProvider::default());
$breakdown = $calculator->calculate(
    $usage,
    new BillingContext(
        billingMode: BillingMode::STANDARD,
        additionalCharges: [
            OpenAIToolPricing::webSearchCalls(3),
        ],
    ),
);

echo $breakdown->totalCostInUsdMicros;
// 37050
```

## Included building blocks

- `AiCosts\CostCalculator`
- `AiCosts\Pricing\StaticPriceProvider`
- `AiCosts\OpenAI\OpenAIResponsesUsageExtractor`
- `AiCosts\OpenAI\OpenAIChatCompletionsUsageExtractor`
- `AiCosts\OpenAI\OpenAIToolPricing`

## Notes

- The packaged price catalog is intentionally static and versioned in the repo.
- Long-context thresholds are only auto-applied where the threshold is explicitly documented in OpenAI model docs.
- `gpt-5.5-pro` pricing is included, but long-context auto-switching is intentionally disabled in this MVP until the threshold rule is modeled explicitly in the catalog.
- Audio token pricing is intentionally blocked in this MVP so the library does not undercount usage silently.
- All calculated values are estimates only; authoritative billing always comes from OpenAI.
- Canonical cost outputs are integer `usd_micros`.
- The package is not affiliated with or endorsed by OpenAI.
- Set a publication license before publishing this package publicly.

## Development

Run the local PHP checks manually with Composer:

```bash
composer lint:syntax
composer lint:phpstan
composer lint:phpmd
composer lint:phpcs
composer lint:all
```

The versioned Git hook lives in `.githooks/pre-commit` and this repository is configured to use it via `git config core.hooksPath .githooks`.

Every `git commit` runs `composer lint:all`; the commit is blocked immediately when one of the checks fails.

## Pricing sources

The bundled pricing catalog is based on the official OpenAI docs as of `2026-05-02`.

- Pricing overview: <https://developers.openai.com/api/docs/pricing>
- GPT-5.4 model page: <https://developers.openai.com/api/docs/models/gpt-5.4/>
- GPT-5.4 pro model page: <https://developers.openai.com/api/docs/models/gpt-5.4-pro>
- OpenAI brand guidelines: <https://openai.com/brand/>
