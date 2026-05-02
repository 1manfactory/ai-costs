<?php

declare(strict_types=1);

return [
    'as_of' => '2026-05-02',
    'source' => 'https://developers.openai.com/api/docs/pricing',
    'models' => [
        'gpt-5.5' => [
            'aliases' => [
                'gpt-5.5-2026-04-23',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 5_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 30_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 10_000_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 1_000_000,
                    'long_context_output_usd_micros_per_million_tokens' => 45_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 2_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 15_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 5_000_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 500_000,
                    'long_context_output_usd_micros_per_million_tokens' => 22_500_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 2_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 15_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 5_000_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 500_000,
                    'long_context_output_usd_micros_per_million_tokens' => 22_500_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 12_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 1_250_000,
                    'output_usd_micros_per_million_tokens' => 75_000_000,
                ],
            ],
        ],
        'gpt-5.5-pro' => [
            'aliases' => [
                'gpt-5.5-pro-2026-04-23',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 30_000_000,
                    'output_usd_micros_per_million_tokens' => 180_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'output_usd_micros_per_million_tokens' => 90_000_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'output_usd_micros_per_million_tokens' => 90_000_000,
                ],
            ],
        ],
        'gpt-5.4' => [
            'aliases' => [
                'gpt-5.4-2026-03-05',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 2_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 15_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 5_000_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 500_000,
                    'long_context_output_usd_micros_per_million_tokens' => 22_500_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_250_000,
                    'cached_input_usd_micros_per_million_tokens' => 125_000,
                    'output_usd_micros_per_million_tokens' => 7_500_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 2_500_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 250_000,
                    'long_context_output_usd_micros_per_million_tokens' => 11_250_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 1_250_000,
                    'cached_input_usd_micros_per_million_tokens' => 125_000,
                    'output_usd_micros_per_million_tokens' => 7_500_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 2_500_000,
                    'long_context_cached_input_usd_micros_per_million_tokens' => 250_000,
                    'long_context_output_usd_micros_per_million_tokens' => 11_250_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 5_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 30_000_000,
                ],
            ],
        ],
        'gpt-5.4-mini' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 750_000,
                    'cached_input_usd_micros_per_million_tokens' => 75_000,
                    'output_usd_micros_per_million_tokens' => 4_500_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 375_000,
                    'cached_input_usd_micros_per_million_tokens' => 37_500,
                    'output_usd_micros_per_million_tokens' => 2_250_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 375_000,
                    'cached_input_usd_micros_per_million_tokens' => 37_500,
                    'output_usd_micros_per_million_tokens' => 2_250_000,
                ],
                'priority' => [
                    'input_usd_micros_per_million_tokens' => 1_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 150_000,
                    'output_usd_micros_per_million_tokens' => 9_000_000,
                ],
            ],
        ],
        'gpt-5.4-nano' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 200_000,
                    'cached_input_usd_micros_per_million_tokens' => 20_000,
                    'output_usd_micros_per_million_tokens' => 1_250_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 100_000,
                    'cached_input_usd_micros_per_million_tokens' => 10_000,
                    'output_usd_micros_per_million_tokens' => 625_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 100_000,
                    'cached_input_usd_micros_per_million_tokens' => 10_000,
                    'output_usd_micros_per_million_tokens' => 625_000,
                ],
            ],
        ],
        'gpt-5.4-pro' => [
            'aliases' => [
                'gpt-5.4-pro-2026-03-05',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 30_000_000,
                    'output_usd_micros_per_million_tokens' => 180_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 60_000_000,
                    'long_context_output_usd_micros_per_million_tokens' => 270_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'output_usd_micros_per_million_tokens' => 90_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 30_000_000,
                    'long_context_output_usd_micros_per_million_tokens' => 135_000_000,
                ],
                'flex' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'output_usd_micros_per_million_tokens' => 90_000_000,
                    'long_context_threshold_input_tokens' => 272000,
                    'long_context_input_usd_micros_per_million_tokens' => 30_000_000,
                    'long_context_output_usd_micros_per_million_tokens' => 135_000_000,
                ],
            ],
        ],
    ],
];
