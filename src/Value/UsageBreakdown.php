<?php

declare(strict_types=1);

namespace AiCosts\Value;

use InvalidArgumentException;

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
    ) {
        foreach (
            [
                'inputTokens' => $this->inputTokens,
                'cachedInputTokens' => $this->cachedInputTokens,
                'outputTokens' => $this->outputTokens,
                'reasoningTokens' => $this->reasoningTokens,
                'inputAudioTokens' => $this->inputAudioTokens,
                'outputAudioTokens' => $this->outputAudioTokens,
            ] as $name => $value
        ) {
            if ($value < 0) {
                throw new InvalidArgumentException(sprintf('%s must be >= 0.', $name));
            }
        }

        if ($this->cachedInputTokens > $this->inputTokens) {
            throw new InvalidArgumentException('cachedInputTokens cannot be greater than inputTokens.');
        }

        if ($this->reasoningTokens > $this->outputTokens) {
            throw new InvalidArgumentException('reasoningTokens cannot be greater than outputTokens.');
        }
    }

    public function uncachedInputTokens(): int
    {
        return $this->inputTokens - $this->cachedInputTokens;
    }
}
