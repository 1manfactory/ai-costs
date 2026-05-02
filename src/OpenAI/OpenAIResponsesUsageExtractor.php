<?php

declare(strict_types=1);

namespace AiCosts\OpenAI;

use AiCosts\Contract\UsageExtractorInterface;
use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\Value\UsageBreakdown;

final class OpenAIResponsesUsageExtractor implements UsageExtractorInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function extract(array $payload): UsageBreakdown
    {
        $model = $payload['model'] ?? null;
        $usage = $payload['usage'] ?? null;

        if (!is_string($model)) {
            throw new InvalidUsagePayload('Expected a Responses payload with `model` and `usage`.');
        }

        $usage = $this->stringKeyArray($usage, 'usage');

        return new UsageBreakdown(
            model: $model,
            inputTokens: $this->intFrom($usage, 'input_tokens'),
            cachedInputTokens: $this->intFromNested($usage, ['input_tokens_details', 'cached_tokens']),
            outputTokens: $this->intFrom($usage, 'output_tokens'),
            reasoningTokens: $this->intFromNested($usage, ['output_tokens_details', 'reasoning_tokens']),
            inputAudioTokens: $this->intFromNested($usage, ['input_tokens_details', 'audio_tokens']),
            outputAudioTokens: $this->intFromNested($usage, ['output_tokens_details', 'audio_tokens']),
            serviceTier: is_string($payload['service_tier'] ?? null) ? $payload['service_tier'] : null,
            source: 'responses',
        );
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
     * @param array<string, mixed> $source
     * @param list<string> $path
     */
    private function intFromNested(array $source, array $path): int
    {
        $current = $source;

        foreach ($path as $segment) {
            $value = $current[$segment] ?? null;

            if ($value === null) {
                return 0;
            }

            if ($segment !== $path[array_key_last($path)]) {
                $current = $this->stringKeyArray($value, $segment);
                continue;
            }

            if (!is_int($value)) {
                throw new InvalidUsagePayload(sprintf('Expected integer usage value for `%s`.', implode('.', $path)));
            }

            return $value;
        }

        return 0;
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
