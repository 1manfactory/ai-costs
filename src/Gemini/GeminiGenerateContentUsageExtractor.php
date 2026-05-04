<?php

declare(strict_types=1);

namespace AiCosts\Gemini;

use AiCosts\Contract\UsageExtractorInterface;
use AiCosts\Exception\InvalidUsagePayload;
use AiCosts\Value\UsageBreakdown;

final class GeminiGenerateContentUsageExtractor implements UsageExtractorInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function extract(array $payload): UsageBreakdown
    {
        $model = $payload['modelVersion'] ?? $payload['model'] ?? null;
        $usageMetadata = $payload['usageMetadata'] ?? null;

        if (!is_string($model)) {
            throw new InvalidUsagePayload(
                'Expected a Gemini GenerateContent payload with `modelVersion` and `usageMetadata`.',
            );
        }

        $usageMetadata = $this->stringKeyArray($usageMetadata, 'usageMetadata');

        return new UsageBreakdown(
            model: $model,
            inputTokens: $this->intFrom($usageMetadata, 'promptTokenCount'),
            cachedInputTokens: $this->intFrom($usageMetadata, 'cachedContentTokenCount'),
            outputTokens: $this->intFrom($usageMetadata, 'candidatesTokenCount'),
            reasoningTokens: $this->intFrom($usageMetadata, 'thoughtsTokenCount'),
            inputAudioTokens: $this->modalityTokenCount($usageMetadata['promptTokensDetails'] ?? null, 'AUDIO'),
            outputAudioTokens: $this->modalityTokenCount(
                $usageMetadata['candidatesTokensDetails'] ?? null,
                'AUDIO',
            ),
            source: 'gemini_generate_content',
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

    private function modalityTokenCount(mixed $value, string $modality): int
    {
        if ($value === null) {
            return 0;
        }

        if (!is_array($value)) {
            throw new InvalidUsagePayload('Expected modality token details to be an array.');
        }

        $total = 0;

        foreach ($value as $index => $item) {
            $detail = $this->stringKeyArray($item, sprintf('modality_details[%d]', $index));
            $detailModality = $detail['modality'] ?? null;
            $tokenCount = $detail['tokenCount'] ?? null;

            if (!is_string($detailModality) || !is_int($tokenCount)) {
                throw new InvalidUsagePayload(
                    'Expected Gemini modality details to contain `modality` and `tokenCount`.',
                );
            }

            if ($detailModality === $modality) {
                $total += $tokenCount;
            }
        }

        return $total;
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
