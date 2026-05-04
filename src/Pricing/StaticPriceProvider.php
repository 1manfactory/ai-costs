<?php

declare(strict_types=1);

namespace AiCosts\Pricing;

use AiCosts\Contract\PriceProviderInterface;
use AiCosts\Enum\BillingMode;
use AiCosts\Exception\UnknownModel;
use AiCosts\Value\PriceCard;

final class StaticPriceProvider implements PriceProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $catalog;

    /**
     * @var array<string, string>
     */
    private readonly array $aliases;

    /**
     * @param array<string, mixed> $catalog
     */
    public function __construct(array $catalog)
    {
        $this->catalog = $catalog;
        $this->aliases = $this->buildAliasMap($this->models());
    }

    public static function default(): self
    {
        /** @var array<string, mixed> $catalog */
        $catalog = require dirname(__DIR__, 2) . '/resources/pricing/catalog.php';

        return new self($catalog);
    }

    public function asOf(): string
    {
        $asOf = $this->catalog['as_of'] ?? 'unknown';

        return is_string($asOf) ? $asOf : 'unknown';
    }

    public function get(string $model, BillingMode $billingMode): PriceCard
    {
        $canonicalModel = $this->resolveModel($model);
        $entry = $this->models()[$canonicalModel] ?? null;

        if ($entry === null) {
            throw new UnknownModel(sprintf('No pricing entry found for model `%s`.', $model));
        }

        $card = $this->cardsFromEntry($entry)[$billingMode->value] ?? null;

        if ($card === null) {
            throw new UnknownModel(
                sprintf('No `%s` pricing configured for model `%s`.', $billingMode->value, $canonicalModel),
            );
        }

        return PriceCard::fromArray($canonicalModel, $billingMode, $card);
    }

    public function pricedModels(): array
    {
        $models = array_keys($this->models());
        sort($models);

        return $models;
    }

    /**
     * @param array<string, array<string, mixed>> $models
     * @return array<string, string>
     */
    private function buildAliasMap(array $models): array
    {
        $aliases = [];

        foreach ($models as $canonical => $entry) {
            foreach ($this->aliasesFromEntry($entry) as $alias) {
                $aliases[$alias] = $canonical;
            }
        }

        return $aliases;
    }

    private function resolveModel(string $model): string
    {
        $models = $this->models();

        if (isset($models[$model])) {
            return $model;
        }

        if (isset($this->aliases[$model])) {
            return $this->aliases[$model];
        }

        $canonicalModels = array_keys($models);
        usort(
            $canonicalModels,
            static fn (string $left, string $right): int => strlen($right) <=> strlen($left),
        );

        foreach ($canonicalModels as $candidate) {
            if (str_starts_with($model, $candidate . '-')) {
                return $candidate;
            }
        }

        throw new UnknownModel(sprintf('No pricing entry found for model `%s`.', $model));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function models(): array
    {
        $models = $this->catalog['models'] ?? [];

        if (!is_array($models)) {
            return [];
        }

        $normalized = [];

        foreach ($models as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $normalized[$key] = $this->stringKeyArray($value);
            }
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    private function aliasesFromEntry(array $entry): array
    {
        $aliases = $entry['aliases'] ?? [];

        if (!is_array($aliases)) {
            return [];
        }

        $normalized = [];

        foreach ($aliases as $alias) {
            if (is_string($alias)) {
                $normalized[] = $alias;
            }
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $entry
     * @return array<string, array<string, mixed>>
     */
    private function cardsFromEntry(array $entry): array
    {
        $cards = $entry['cards'] ?? [];

        if (!is_array($cards)) {
            return [];
        }

        $normalized = [];

        foreach ($cards as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $normalized[$key] = $this->stringKeyArray($value);
            }
        }

        return $normalized;
    }

    /**
     * @param array<mixed> $values
     * @return array<string, mixed>
     */
    private function stringKeyArray(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
