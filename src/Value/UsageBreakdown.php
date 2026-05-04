<?php

declare(strict_types=1);

namespace AiCosts\Value;

use InvalidArgumentException;

/**
 * @SuppressWarnings("PHPMD.ExcessiveParameterList")
 */
final readonly class UsageBreakdown
{
    public function __construct(
        public string $model,
        public int $inputTokens,
        public int $cachedInputTokens,
        public int $outputTokens,
        public int $reasoningTokens = 0,
        public int $inputAudioTokens = 0,
        public int $outputAudioTokens = 0,
        public ?string $serviceTier = null,
        public string $source = 'manual',
        public int $cacheWrite5mInputTokens = 0,
        public int $cacheWrite1hInputTokens = 0,
    ) {
        foreach (
            [
                'inputTokens' => $this->inputTokens,
                'cachedInputTokens' => $this->cachedInputTokens,
                'outputTokens' => $this->outputTokens,
                'reasoningTokens' => $this->reasoningTokens,
                'inputAudioTokens' => $this->inputAudioTokens,
                'outputAudioTokens' => $this->outputAudioTokens,
                'cacheWrite5mInputTokens' => $this->cacheWrite5mInputTokens,
                'cacheWrite1hInputTokens' => $this->cacheWrite1hInputTokens,
            ] as $name => $value
        ) {
            if ($value < 0) {
                throw new InvalidArgumentException(sprintf('%s must be >= 0.', $name));
            }
        }

        if (
            $this->cachedInputTokens + $this->cacheWrite5mInputTokens + $this->cacheWrite1hInputTokens
            > $this->inputTokens
        ) {
            throw new InvalidArgumentException(
                'The sum of cached and cache-write input tokens cannot be greater than inputTokens.',
            );
        }

        if ($this->reasoningTokens > $this->outputTokens) {
            throw new InvalidArgumentException('reasoningTokens cannot be greater than outputTokens.');
        }
    }

    public function uncachedInputTokens(): int
    {
        return $this->inputTokens
            - $this->cachedInputTokens
            - $this->cacheWrite5mInputTokens
            - $this->cacheWrite1hInputTokens;
    }
}
