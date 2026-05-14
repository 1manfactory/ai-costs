# ai-costs

`ai-costs` is an unofficial PHP library for calculating LLM API costs from usage payloads.

It is designed to stay SDK-agnostic: feed it an OpenAI, Anthropic Claude, or Google Gemini usage payload, or a normalized usage object, and it returns a detailed cost breakdown.

> [!WARNING]
> This library provides estimates only. I do not guarantee the accuracy, completeness, or fitness of the calculated values for any billing purpose. The amounts actually billed by the provider are authoritative and always take precedence.

## Units

All calculated cost values are returned as integers in `usd_microcent`.

- `usd_microcent` is the only money unit used by this package
- `1 USD = 100_000 usd_microcent`
- `1 usd_microcent = 0.00001 USD = 0.001 cents`
- `1 cent = 1_000 usd_microcent`
- Provider APIs do not return money amounts; they return usage counts, and this package converts those counts into estimated `usd_microcent`
- Public variable and method names use explicit unit suffixes like `InUsdMicrocent`
- The package returns integers only

## Current scope

- OpenAI `Responses API` usage extraction
- OpenAI `Chat Completions API` usage extraction
- Anthropic `Messages API` usage extraction
- Google Gemini `generateContent` usage extraction
- Text token pricing for selected GPT-5.4 and GPT-5.5 family models
- Text token pricing for current Claude and Gemini 2.5 text-output models
- Automatic long-context pricing for `gpt-5.5`, `gpt-5.4`, `gpt-5.4-pro`, and `gemini-2.5-pro`
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

echo $breakdown->totalCostInUsdMicrocent;
// 3705
```

## Included building blocks

- `AiCosts\CostCalculator`
- `AiCosts\Pricing\StaticPriceProvider`
- `AiCosts\OpenAI\OpenAIResponsesUsageExtractor`
- `AiCosts\OpenAI\OpenAIChatCompletionsUsageExtractor`
- `AiCosts\Anthropic\AnthropicMessagesUsageExtractor`
- `AiCosts\Gemini\GeminiGenerateContentUsageExtractor`
- `AiCosts\OpenAI\OpenAIToolPricing`

## Notes

- The packaged price catalog is intentionally static and versioned in the repo.
- Long-context thresholds are only auto-applied where the threshold is explicitly documented in OpenAI model docs.
- Anthropic prompt caching reads plus 5-minute and 1-hour cache writes are supported when the API response includes the detailed `usage.cache_creation` breakdown.
- Gemini support currently targets the stable `gemini-2.5-*` text-output models included in the bundled catalog.
- `gpt-5.5-pro` pricing is included, but long-context auto-switching is intentionally disabled in this MVP until the threshold rule is modeled explicitly in the catalog.
- Explicit Gemini cache storage charges and grounding/tool surcharges are not auto-derived from a single response payload in this MVP.
- Audio token pricing is intentionally blocked in this MVP so the library does not undercount usage silently.
- All calculated values are estimates only; authoritative billing always comes from OpenAI.
- Claude and Gemini usage is also estimated from official public pricing docs; provider billing remains authoritative.
- Canonical cost outputs are integer `usd_microcent`.
- The package is not affiliated with or endorsed by OpenAI, Anthropic, or Google.
- This package is licensed under the MIT License.

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

## Release flow

This repository uses a GitHub Actions workflow at `.github/workflows/release-tag.yml` to create a new patch tag on every push to `main`.

- First release tag: `v0.1.0`
- Later pushes on `main`: `v0.1.1`, `v0.1.2`, `v0.1.3`, ...

That means the publishing path is:

`local repository -> GitHub main -> automatic Git tag -> Packagist update`

After the repository is submitted once on Packagist, each new Git tag becomes a new Composer-installable package version.

## Pricing sources

The bundled pricing catalog is based on official provider docs as of `2026-05-13`.

- OpenAI pricing overview: <https://developers.openai.com/api/docs/pricing>
- OpenAI GPT-5.4 model page: <https://developers.openai.com/api/docs/models/gpt-5.4/>
- OpenAI GPT-5.4 pro model page: <https://developers.openai.com/api/docs/models/gpt-5.4-pro>
- Anthropic pricing: <https://platform.claude.com/docs/en/about-claude/pricing>
- Anthropic model overview: <https://platform.claude.com/docs/en/about-claude/models/overview>
- Gemini pricing: <https://ai.google.dev/gemini-api/docs/pricing>
- Gemini generateContent response schema: <https://ai.google.dev/api/generate-content>
