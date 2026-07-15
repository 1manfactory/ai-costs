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
- Text token pricing for selected GPT-5.6, GPT-5.5, GPT-5.4, Claude, and Gemini 2.5 models
- GPT-5.6 Sol, Terra, and Luna across `standard`, `batch`, `flex`, and selected `priority`
- Generic OpenAI cache-write token pricing and Anthropic 5-minute and 1-hour cache writes
- Automatic long-context pricing where a documented threshold exists
- Explicit short- or long-context selection through `ContextPricingMode`
- Time-dependent static price cards such as Claude Sonnet 5 introductory and regular pricing
- Helper charges for web search, file search, fixed 20-minute container sessions, and eligible minute-billed container sessions

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

echo $breakdown->inputCostInUsdMicrocent;
echo $breakdown->cachedInputCostInUsdMicrocent;
echo $breakdown->outputCostInUsdMicrocent;
echo $breakdown->additionalChargesInUsdMicrocent;
echo $breakdown->totalCostInUsdMicrocent;
```

## Included building blocks

- `AiCosts\CostCalculator`
- `AiCosts\Pricing\StaticPriceProvider`
- `AiCosts\OpenAI\OpenAIResponsesUsageExtractor`
- `AiCosts\OpenAI\OpenAIChatCompletionsUsageExtractor`
- `AiCosts\Anthropic\AnthropicMessagesUsageExtractor`
- `AiCosts\Gemini\GeminiGenerateContentUsageExtractor`
- `AiCosts\OpenAI\OpenAIToolPricing`

## Pricing catalog metadata

```php
$catalog = StaticPriceProvider::default();

echo $catalog->version;
echo $catalog->provider('openai')->verifiedAt;

foreach ($catalog->provider('openai')->sourceUrls as $sourceUrl) {
    echo $sourceUrl . PHP_EOL;
}
```

- `version` identifies the revision of the bundled pricing catalog.
- `verifiedAt` is the last verification date recorded for that provider.
- `sourceUrls` lists the official documentation URLs used for that provider and the global catalog.
- The metadata does not prove that prices remained unchanged after the recorded verification date.

## More examples

### GPT-5.6 with automatic long-context detection

```php
use AiCosts\Value\UsageBreakdown;

$usage = new UsageBreakdown(
    model: 'gpt-5.6',
    inputTokens: 300_000,
    cachedInputTokens: 20_000,
    outputTokens: 10_000,
    cacheWriteInputTokens: 30_000,
);
```

### GPT-5.5 Pro with explicit long context

```php
use AiCosts\Enum\ContextPricingMode;

new BillingContext(
    billingMode: BillingMode::STANDARD,
    contextPricingMode: ContextPricingMode::LONG,
);
```

Long-context prices for `gpt-5.5-pro` are included in the catalog. Automatic switching stays disabled because the official model page does not document a threshold, so `ContextPricingMode::LONG` is the explicit opt-in.

### Sonnet 5 with a fixed pricing date

```php
use DateTimeImmutable;

$provider = StaticPriceProvider::default(
    new DateTimeImmutable('2026-08-31'),
);

$provider = StaticPriceProvider::default(
    new DateTimeImmutable('2026-09-01'),
);
```

### Minute-billed container sessions

```php
OpenAIToolPricing::containerSessionMinutes(
    memoryGb: 4,
    durationMinutes: 13,
);
```

## Notes

- The packaged price catalog is intentionally static and versioned in the repo.
- Long-context thresholds are only auto-applied where the threshold is explicitly documented in OpenAI model docs.
- `gpt-5.5-pro` long-context prices are included, but automatic switching remains disabled because no official threshold is documented.
- Anthropic prompt caching reads plus 5-minute and 1-hour cache writes are supported when the API response includes the detailed `usage.cache_creation` breakdown.
- OpenAI generic cache writes are supported when usage payloads report `cache_write_tokens`.
- Gemini support currently targets the stable `gemini-2.5-*` text-output models included in the bundled catalog.
- Explicit Gemini cache storage charges and grounding/tool surcharges are not auto-derived from a single response payload in this MVP.
- Audio token pricing is intentionally blocked in this MVP so the library does not undercount usage silently.
- All calculated values are estimates only; authoritative billing always comes from the respective provider.
- Claude and Gemini usage is estimated from official public pricing docs for the selected models bundled here; provider billing remains authoritative.
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

The bundled pricing catalog is based on official provider docs as of `2026-07-15`.

On `2026-07-15`, the bundled model prices and helper charges were re-checked against the official pricing pages linked below. Gemini 2.5 catalog values remained unchanged in this verification pass; OpenAI and Claude entries were expanded to include GPT-5.6, explicit OpenAI cache writes, minute-billed eligible container sessions, Claude Fable 5, Claude Opus 4.8, and dated Claude Sonnet 5 pricing.

| Provider | Included model families | Billing modes in catalog | Last verified |
| --- | --- | --- | --- |
| OpenAI | `gpt-5.6-sol`, `gpt-5.6-terra`, `gpt-5.6-luna`, `gpt-5.5`, `gpt-5.5-pro`, `gpt-5.4`, `gpt-5.4-mini`, `gpt-5.4-nano`, `gpt-5.4-pro`, `gpt-5`, `gpt-5-mini`, `gpt-5-nano`, `gpt-5-pro`, `gpt-4.1`, `gpt-4.1-mini`, `gpt-4.1-nano`, `gpt-4o`, `gpt-4o-mini`, `o1`, `o1-mini`, `o1-pro`, `o3`, `o3-mini`, `o3-pro`, `o4-mini`, `o3-deep-research`, `o4-mini-deep-research`, `codex-mini-latest`, `computer-use-preview`, `gpt-4-turbo`, `gpt-4`, `gpt-3.5-turbo`, `gpt-3.5-turbo-instruct` | `standard`, selected `batch`, selected `flex`, selected `priority` | `2026-07-15` |
| Anthropic | `claude-fable-5`, `claude-opus-4-8`, `claude-opus-4-7`, `claude-sonnet-5`, `claude-sonnet-4-6`, `claude-haiku-4-5-20251001` plus alias `claude-haiku-4-5` | `standard`, `batch` | `2026-07-15` |
| Google Gemini | `gemini-2.5-pro`, `gemini-2.5-flash`, `gemini-2.5-flash-lite` | `standard`, `batch`, `flex`, `priority` | `2026-07-15` |

- OpenAI pricing overview: <https://developers.openai.com/api/docs/pricing>
- OpenAI prompt caching: <https://developers.openai.com/api/docs/guides/prompt-caching>
- OpenAI GPT-5.6 Sol model page: <https://developers.openai.com/api/docs/models/gpt-5.6-sol>
- OpenAI GPT-5.6 Terra model page: <https://developers.openai.com/api/docs/models/gpt-5.6-terra>
- OpenAI GPT-5.6 Luna model page: <https://developers.openai.com/api/docs/models/gpt-5.6-luna>
- OpenAI GPT-5.5 Pro model page: <https://developers.openai.com/api/docs/models/gpt-5.5-pro>
- Anthropic pricing: <https://platform.claude.com/docs/en/about-claude/pricing>
- Anthropic model overview: <https://platform.claude.com/docs/en/about-claude/models/overview>
- Gemini pricing: <https://ai.google.dev/gemini-api/docs/pricing>
- Gemini generateContent response schema: <https://ai.google.dev/api/generate-content>
