<?php

declare(strict_types=1);

namespace AiCosts\Pricing;

use AiCosts\Enum\BillingMode;
use AiCosts\Exception\InvalidPricingCatalog;
use AiCosts\Exception\UnavailablePricingPeriod;
use DateTimeImmutable;
use DateTimeZone;

final readonly class StaticPricingPeriods
{
    public function __construct(
        private DateTimeImmutable $pricingDate,
    ) {
    }

    /**
     * @param array<string, mixed> $cardEntry
     */
    public function assertCardEntryIsValid(string $model, string $billingMode, array $cardEntry): void
    {
        if (!array_key_exists('periods', $cardEntry)) {
            return;
        }

        $periods = $this->validatedPeriods($cardEntry['periods'] ?? null, $model, $billingMode);
        $previousUntil = null;
        $periodIndex = 0;

        foreach ($periods as $period) {
            [$from, $until] = $this->validatedPeriodBounds($period, $model, $billingMode);
            $this->assertChronologicalPeriodOrdering($model, $billingMode, $periodIndex, $previousUntil, $from);
            $this->assertPeriodPricesExist($period['prices'] ?? null, $model, $billingMode);
            $previousUntil = $until;
            $periodIndex++;
        }
    }

    /**
     * @param array<string, mixed> $cardEntry
     * @return array<string, mixed>
     */
    public function resolveCardData(string $model, BillingMode $billingMode, array $cardEntry): array
    {
        if (!array_key_exists('periods', $cardEntry)) {
            return $cardEntry;
        }

        $matchedPeriod = null;
        $periods = $this->validatedPeriods($cardEntry['periods'] ?? null, $model, $billingMode->value);

        foreach ($periods as $period) {
            if ($this->periodMatchesPricingDate($period)) {
                if ($matchedPeriod !== null) {
                    throw new InvalidPricingCatalog(
                        sprintf(
                            'Multiple pricing periods match `%s` for model `%s` and `%s` billing mode.',
                            $this->pricingDateString(),
                            $model,
                            $billingMode->value,
                        ),
                    );
                }

                $matchedPeriod = $period;
            }
        }

        if ($matchedPeriod === null) {
            throw new UnavailablePricingPeriod(
                sprintf(
                    'No pricing period matches `%s` for model `%s` and `%s` billing mode.',
                    $this->pricingDateString(),
                    $model,
                    $billingMode->value,
                ),
            );
        }

        $prices = $matchedPeriod['prices'] ?? null;

        if (!is_array($prices)) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Pricing period prices must be defined for model `%s` and `%s` billing mode.',
                    $model,
                    $billingMode->value,
                ),
            );
        }

        return $this->stringKeyArray($prices);
    }

    /**
     * @param mixed $periods
     * @return list<array<string, mixed>>
     */
    private function validatedPeriods(mixed $periods, string $model, string $billingMode): array
    {
        if (!is_array($periods) || $periods === []) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Pricing periods must be a non-empty list for model `%s` and `%s` billing mode.',
                    $model,
                    $billingMode,
                ),
            );
        }

        $normalized = [];

        foreach ($periods as $period) {
            if (!is_array($period)) {
                throw new InvalidPricingCatalog(
                    sprintf(
                        'Pricing periods must contain arrays for model `%s` and `%s` billing mode.',
                        $model,
                        $billingMode,
                    ),
                );
            }

            $normalized[] = $this->stringKeyArray($period);
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $period
     */
    private function periodMatchesPricingDate(array $period): bool
    {
        $pricingDate = $this->pricingDateString();
        $from = $period['effective_from'] ?? null;
        $until = $period['effective_until'] ?? null;

        if (is_string($from) && $pricingDate < $from) {
            return false;
        }

        if (is_string($until) && $pricingDate > $until) {
            return false;
        }

        return true;
    }

    private function pricingDateString(): string
    {
        return $this->pricingDate->format('Y-m-d');
    }

    private function nullablePeriodDate(
        mixed $value,
        string $field,
        string $model,
        string $billingMode,
    ): ?string {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) || !$this->isValidDateString($value)) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Expected `%s` to be a YYYY-MM-DD date for model `%s` and `%s` billing mode.',
                    $field,
                    $model,
                    $billingMode,
                ),
            );
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $period
     * @return array{0: ?string, 1: ?string}
     */
    private function validatedPeriodBounds(array $period, string $model, string $billingMode): array
    {
        $from = $this->nullablePeriodDate($period['effective_from'] ?? null, 'effective_from', $model, $billingMode);
        $until = $this->nullablePeriodDate(
            $period['effective_until'] ?? null,
            'effective_until',
            $model,
            $billingMode,
        );

        if ($from !== null && $until !== null && $from > $until) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Pricing period dates are reversed for model `%s` and `%s` billing mode.',
                    $model,
                    $billingMode,
                ),
            );
        }

        return [$from, $until];
    }

    private function assertChronologicalPeriodOrdering(
        string $model,
        string $billingMode,
        int $periodIndex,
        ?string $previousUntil,
        ?string $from,
    ): void {
        if ($previousUntil === null && $periodIndex > 0) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Pricing periods must be in chronological order for model `%s` and `%s` billing mode.',
                    $model,
                    $billingMode,
                ),
            );
        }

        if ($previousUntil !== null && ($from === null || $from <= $previousUntil)) {
            throw new InvalidPricingCatalog(
                sprintf(
                    'Pricing periods overlap for model `%s` and `%s` billing mode.',
                    $model,
                    $billingMode,
                ),
            );
        }
    }

    private function assertPeriodPricesExist(mixed $prices, string $model, string $billingMode): void
    {
        if (is_array($prices)) {
            return;
        }

        throw new InvalidPricingCatalog(
            sprintf(
                'Pricing period prices must be defined for model `%s` and `%s` billing mode.',
                $model,
                $billingMode,
            ),
        );
    }

    private function isValidDateString(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));

        return $date !== false && $date->format('Y-m-d') === $value;
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
