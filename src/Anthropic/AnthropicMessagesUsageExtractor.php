<?php

declare(strict_types=1);

namespace AiCosts\Anthropic;

use AiCosts\Contract\UsageExtractorInterface;
use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\Value\UsageBreakdown;

final class AnthropicMessagesUsageExtractor implements UsageExtractorInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function extract(array $payload): UsageBreakdown
    {
        $model = $payload['model'] ?? null;
        $usage = $payload['usage'] ?? null;

        if (!is_string($model)) {
            throw new InvalidUsagePayload('Expected an Anthropic Messages payload with `model` and `usage`.');
        }

        $usage = $this->stringKeyArray($usage, 'usage');
        $baseInputTokens = $this->intFrom($usage, 'input_tokens');
        $cacheReadInputTokens = $this->intFrom($usage, 'cache_read_input_tokens');
        $cacheCreationInputTokens = $this->intFrom($usage, 'cache_creation_input_tokens');
        [$cacheWrite5mInputTokens, $cacheWrite1hInputTokens] = $this->cacheWriteBreakdown(
            $cacheCreationInputTokens,
            $usage['cache_creation'] ?? null,
        );

        return new UsageBreakdown(
            model: $model,
            inputTokens: $baseInputTokens + $cacheReadInputTokens + $cacheCreationInputTokens,
            cachedInputTokens: $cacheReadInputTokens,
            outputTokens: $this->intFrom($usage, 'output_tokens'),
            serviceTier: is_string($usage['service_tier'] ?? null) ? $usage['service_tier'] : null,
            source: 'anthropic_messages',
            cacheWrite5mInputTokens: $cacheWrite5mInputTokens,
            cacheWrite1hInputTokens: $cacheWrite1hInputTokens,
        );
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function cacheWriteBreakdown(int $totalCacheCreationInputTokens, mixed $cacheCreation): array
    {
        if ($totalCacheCreationInputTokens === 0) {
            return [0, 0];
        }

        $cacheCreation = $this->stringKeyArray($cacheCreation, 'usage.cache_creation');
        $cacheWrite5mInputTokens = $this->intFrom($cacheCreation, 'ephemeral_5m_input_tokens');
        $cacheWrite1hInputTokens = $this->intFrom($cacheCreation, 'ephemeral_1h_input_tokens');

        if ($cacheWrite5mInputTokens + $cacheWrite1hInputTokens !== $totalCacheCreationInputTokens) {
            throw new InvalidUsagePayload(
                'Expected `usage.cache_creation` to match `usage.cache_creation_input_tokens`.',
            );
        }

        return [$cacheWrite5mInputTokens, $cacheWrite1hInputTokens];
    }

    /**
     * @param array<string, mixed> $source
     */
    private function intFrom(array $source, string $key): int
    {
        $value = $source[$key] ?? 0;

        if (!is_int($value)) {
            throw new InvalidUsagePayload(sprintf('Expected integer usage value for `%s`.', $key));
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function stringKeyArray(mixed $value, string $name): array
    {
        if (!is_array($value)) {
            throw new InvalidUsagePayload(sprintf('Expected `%s` to be an array.', $name));
        }

        $normalized = [];

        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                throw new InvalidUsagePayload(sprintf('Expected `%s` to contain string keys only.', $name));
            }

            $normalized[$key] = $item;
        }

        return $normalized;
    }
}
