<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $catalogs */
$catalogs = [
    require __DIR__ . '/openai.php',
    require __DIR__ . '/anthropic.php',
    require __DIR__ . '/gemini.php',
];

$models = [];
$asOf = 'unknown';
$sourceUrls = [];
$providers = [];
$version = '2026-07-08';

foreach ($catalogs as $catalog) {
    $catalogAsOf = $catalog['as_of'] ?? null;

    if (is_string($catalogAsOf)) {
        $asOf = $catalogAsOf;
    }

    $providerKey = $catalog['provider'] ?? null;
    $providerName = $catalog['name'] ?? null;
    $providerSourceUrls = $catalog['source_urls'] ?? null;

    if (is_string($providerKey) && is_string($providerName) && is_array($providerSourceUrls)) {
        $providers[$providerKey] = [
            'name' => $providerName,
            'verified_at' => $catalogAsOf,
            'source_urls' => $providerSourceUrls,
        ];

        foreach ($providerSourceUrls as $sourceUrl) {
            if (is_string($sourceUrl)) {
                $sourceUrls[] = $sourceUrl;
            }
        }
    }

    $catalogModels = $catalog['models'] ?? null;

    if (!is_array($catalogModels)) {
        continue;
    }

    foreach ($catalogModels as $key => $value) {
        if (is_string($key) && is_array($value)) {
            $models[$key] = $value;
        }
    }
}

return [
    'version' => $version,
    'as_of' => $asOf,
    'source_urls' => array_values(array_unique($sourceUrls)),
    'providers' => $providers,
    'models' => $models,
];
