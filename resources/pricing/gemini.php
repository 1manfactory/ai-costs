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
                    'input_usd_micros_per_million_tokens' => 1_250_000,
                    'cached_input_usd_micros_per_million_tokens' => 125_000,
                    'output_usd_micros_per_million_tokens' => 10_000_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_micros_per_million_tokens' => 2_500_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 250_000,
                    'long_context_output_usd_micros_per_million_tokens' => 15_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 625_000,
                    'cached_input_usd_micros_per_million_tokens' => 125_000,
                    'output_usd_micros_per_million_tokens' => 5_000_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_micros_per_million_tokens' => 1_250_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 250_000,
                    'long_context_output_usd_micros_per_million_tokens' => 7_500_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 625_000,
                    'cached_input_usd_micros_per_million_tokens' => 125_000,
                    'output_usd_micros_per_million_tokens' => 5_000_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_micros_per_million_tokens' => 1_250_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 250_000,
                    'long_context_output_usd_micros_per_million_tokens' => 7_500_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 2_250_000,
                    'cached_input_usd_micros_per_million_tokens' => 225_000,
                    'output_usd_micros_per_million_tokens' => 18_000_000,
                    'long_context_threshold_input_tokens' => 200000,
                    'long_context_input_usd_micros_per_million_tokens' => 4_500_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 450_000,
                    'long_context_output_usd_micros_per_million_tokens' => 27_000_000,
                ],
            ],
        ],
        'gemini-2.5-flash' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 300_000,
                    'cached_input_usd_micros_per_million_tokens' => 30_000,
                    'output_usd_micros_per_million_tokens' => 2_500_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 150_000,
                    'cached_input_usd_micros_per_million_tokens' => 30_000,
                    'output_usd_micros_per_million_tokens' => 1_250_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 150_000,
                    'cached_input_usd_micros_per_million_tokens' => 30_000,
                    'output_usd_micros_per_million_tokens' => 1_250_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 540_000,
                    'cached_input_usd_micros_per_million_tokens' => 54_000,
                    'output_usd_micros_per_million_tokens' => 4_500_000,
                ],
            ],
        ],
        'gemini-2.5-flash-lite' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 100_000,
                    'cached_input_usd_micros_per_million_tokens' => 10_000,
                    'output_usd_micros_per_million_tokens' => 400_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 50_000,
                    'cached_input_usd_micros_per_million_tokens' => 10_000,
                    'output_usd_micros_per_million_tokens' => 200_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 50_000,
                    'cached_input_usd_micros_per_million_tokens' => 10_000,
                    'output_usd_micros_per_million_tokens' => 200_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 180_000,
                    'cached_input_usd_micros_per_million_tokens' => 18_000,
                    'output_usd_micros_per_million_tokens' => 720_000,
                ],
            ],
        ],
    ],
];
