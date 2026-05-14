<?php

declare(strict_types=1);

return [
    'as_of' => '2026-05-13',
    'source' => 'https://ai.google.dev/gemini-api/docs/pricing',
    'models' => [
        'gemini-2.5-pro' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 125_000,
                    'cached_input_usd_microcent_per_million_tokens' => 12_500,
                    'output_usd_microcent_per_million_tokens' => 1_000_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_microcent_per_million_tokens' => 250_000,
                    'long_context_cached_input_usd_microcent_per_million_tokens' => 25_000,
                    'long_context_output_usd_microcent_per_million_tokens' => 1_500_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 62_500,
                    'cached_input_usd_microcent_per_million_tokens' => 12_500,
                    'output_usd_microcent_per_million_tokens' => 500_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_microcent_per_million_tokens' => 125_000,
                    'long_context_cached_input_usd_microcent_per_million_tokens' => 25_000,
                    'long_context_output_usd_microcent_per_million_tokens' => 750_000,
                ],
                'flex' => [
                    'input_usd_microcent_per_million_tokens' => 62_500,
                    'cached_input_usd_microcent_per_million_tokens' => 12_500,
                    'output_usd_microcent_per_million_tokens' => 500_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_microcent_per_million_tokens' => 125_000,
                    'long_context_cached_input_usd_microcent_per_million_tokens' => 25_000,
                    'long_context_output_usd_microcent_per_million_tokens' => 750_000,
                ],
                'priority' => [
                    'input_usd_microcent_per_million_tokens' => 225_000,
                    'cached_input_usd_microcent_per_million_tokens' => 22_500,
                    'output_usd_microcent_per_million_tokens' => 1_800_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_microcent_per_million_tokens' => 450_000,
                    'long_context_cached_input_usd_microcent_per_million_tokens' => 45_000,
                    'long_context_output_usd_microcent_per_million_tokens' => 2_700_000,
                ],
            ],
        ],
        'gemini-2.5-flash' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 30_000,
                    'cached_input_usd_microcent_per_million_tokens' => 3_000,
                    'output_usd_microcent_per_million_tokens' => 250_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 15_000,
                    'cached_input_usd_microcent_per_million_tokens' => 3_000,
                    'output_usd_microcent_per_million_tokens' => 125_000,
                ],
                'flex' => [
                    'input_usd_microcent_per_million_tokens' => 15_000,
                    'cached_input_usd_microcent_per_million_tokens' => 3_000,
                    'output_usd_microcent_per_million_tokens' => 125_000,
                ],
                'priority' => [
                    'input_usd_microcent_per_million_tokens' => 54_000,
                    'cached_input_usd_microcent_per_million_tokens' => 5_400,
                    'output_usd_microcent_per_million_tokens' => 450_000,
                ],
            ],
        ],
        'gemini-2.5-flash-lite' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 10_000,
                    'cached_input_usd_microcent_per_million_tokens' => 1_000,
                    'output_usd_microcent_per_million_tokens' => 40_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 5_000,
                    'cached_input_usd_microcent_per_million_tokens' => 1_000,
                    'output_usd_microcent_per_million_tokens' => 20_000,
                ],
                'flex' => [
                    'input_usd_microcent_per_million_tokens' => 5_000,
                    'cached_input_usd_microcent_per_million_tokens' => 1_000,
                    'output_usd_microcent_per_million_tokens' => 20_000,
                ],
                'priority' => [
                    'input_usd_microcent_per_million_tokens' => 18_000,
                    'cached_input_usd_microcent_per_million_tokens' => 1_800,
                    'output_usd_microcent_per_million_tokens' => 72_000,
                ],
            ],
        ],
    ],
];
