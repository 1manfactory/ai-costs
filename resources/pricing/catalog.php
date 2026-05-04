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
$sources = [];

foreach ($catalogs as $catalog) {
    $catalogAsOf = $catalog['as_of'] ?? null;

    if (is_string($catalogAsOf)) {
        $asOf = $catalogAsOf;
    }

    $source = $catalog['source'] ?? null;

    if (is_string($source)) {
        $sources[] = $source;
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
    'as_of' => $asOf,
    'sources' => $sources,
    'models' => $models,
];
