<?php

declare(strict_types=1);

return [
    'modelVersion' => 'gemini-2.5-pro-001',
    'usageMetadata' => [
        'promptTokenCount' => 1200,
        'cachedContentTokenCount' => 200,
        'candidatesTokenCount' => 300,
        'thoughtsTokenCount' => 80,
        'promptTokensDetails' => [
            [
                'modality' => 'TEXT',
                'tokenCount' => 1200,
            ],
        ],
        'cacheTokensDetails' => [
            [
                'modality' => 'TEXT',
                'tokenCount' => 200,
            ],
        ],
        'candidatesTokensDetails' => [
            [
                'modality' => 'TEXT',
                'tokenCount' => 300,
            ],
        ],
    ],
];
