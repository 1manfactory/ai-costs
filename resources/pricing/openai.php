<?php

declare(strict_types=1);

return [
    'as_of' => '2026-05-05',
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
        'gpt-4.1' => [
            'aliases' => [
                'gpt-4.1-2025-04-14',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 2_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 8_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 4_000_000,
                ],
            ],
        ],
        'gpt-4.1-mini' => [
            'aliases' => [
                'gpt-4.1-mini-2025-04-14',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 400_000,
                    'cached_input_usd_micros_per_million_tokens' => 100_000,
                    'output_usd_micros_per_million_tokens' => 1_600_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 200_000,
                    'cached_input_usd_micros_per_million_tokens' => 50_000,
                    'output_usd_micros_per_million_tokens' => 800_000,
                ],
            ],
        ],
        'gpt-4.1-nano' => [
            'aliases' => [
                'gpt-4.1-nano-2025-04-14',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 100_000,
                    'cached_input_usd_micros_per_million_tokens' => 25_000,
                    'output_usd_micros_per_million_tokens' => 400_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 50_000,
                    'cached_input_usd_micros_per_million_tokens' => 12_500,
                    'output_usd_micros_per_million_tokens' => 200_000,
                ],
            ],
        ],
        'gpt-4o' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 2_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 1_250_000,
                    'output_usd_micros_per_million_tokens' => 10_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_250_000,
                    'cached_input_usd_micros_per_million_tokens' => 625_000,
                    'output_usd_micros_per_million_tokens' => 5_000_000,
                ],
            ],
        ],
        'gpt-4o-mini' => [
            'aliases' => [
                'gpt-4o-mini-2024-07-18',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 150_000,
                    'cached_input_usd_micros_per_million_tokens' => 75_000,
                    'output_usd_micros_per_million_tokens' => 600_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 75_000,
                    'cached_input_usd_micros_per_million_tokens' => 37_500,
                    'output_usd_micros_per_million_tokens' => 300_000,
                ],
            ],
        ],
        'o1' => [
            'aliases' => [
                'o1-2024-12-17',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 7_500_000,
                    'output_usd_micros_per_million_tokens' => 60_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 7_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 3_750_000,
                    'output_usd_micros_per_million_tokens' => 30_000_000,
                ],
            ],
        ],
        'o1-mini' => [
            'aliases' => [
                'o1-mini-2024-09-12',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_100_000,
                    'cached_input_usd_micros_per_million_tokens' => 550_000,
                    'output_usd_micros_per_million_tokens' => 4_400_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 550_000,
                    'cached_input_usd_micros_per_million_tokens' => 275_000,
                    'output_usd_micros_per_million_tokens' => 2_200_000,
                ],
            ],
        ],
        'o1-pro' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 150_000_000,
                    'output_usd_micros_per_million_tokens' => 600_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 75_000_000,
                    'output_usd_micros_per_million_tokens' => 300_000_000,
                ],
            ],
        ],
        'o3' => [
            'aliases' => [
                'o3-2025-04-16',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 2_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 8_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 4_000_000,
                ],
            ],
        ],
        'o3-mini' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_100_000,
                    'cached_input_usd_micros_per_million_tokens' => 550_000,
                    'output_usd_micros_per_million_tokens' => 4_400_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 550_000,
                    'cached_input_usd_micros_per_million_tokens' => 275_000,
                    'output_usd_micros_per_million_tokens' => 2_200_000,
                ],
            ],
        ],
        'o3-pro' => [
            'aliases' => [
                'o3-pro-2025-06-10',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 20_000_000,
                    'output_usd_micros_per_million_tokens' => 80_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 10_000_000,
                    'output_usd_micros_per_million_tokens' => 40_000_000,
                ],
            ],
        ],
        'o4-mini' => [
            'aliases' => [
                'o4-mini-2025-04-16',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_100_000,
                    'cached_input_usd_micros_per_million_tokens' => 275_000,
                    'output_usd_micros_per_million_tokens' => 4_400_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 550_000,
                    'cached_input_usd_micros_per_million_tokens' => 137_500,
                    'output_usd_micros_per_million_tokens' => 2_200_000,
                ],
            ],
        ],
        'o3-deep-research' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 10_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 2_500_000,
                    'output_usd_micros_per_million_tokens' => 40_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 5_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 1_250_000,
                    'output_usd_micros_per_million_tokens' => 20_000_000,
                ],
            ],
        ],
        'o4-mini-deep-research' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 2_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 8_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 4_000_000,
                ],
            ],
        ],
        'codex-mini-latest' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 375_000,
                    'output_usd_micros_per_million_tokens' => 6_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 750_000,
                    'cached_input_usd_micros_per_million_tokens' => 187_500,
                    'output_usd_micros_per_million_tokens' => 3_000_000,
                ],
            ],
        ],
        'computer-use-preview' => [
            'aliases' => [
                'computer-use-preview-2025-03-11',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 3_000_000,
                    'output_usd_micros_per_million_tokens' => 12_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_500_000,
                    'output_usd_micros_per_million_tokens' => 6_000_000,
                ],
            ],
        ],
        'gpt-4-turbo' => [
            'aliases' => [
                'gpt-4-turbo-2024-04-09',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 10_000_000,
                    'output_usd_micros_per_million_tokens' => 30_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 5_000_000,
                    'output_usd_micros_per_million_tokens' => 15_000_000,
                ],
            ],
        ],
        'gpt-4' => [
            'aliases' => [
                'gpt-4-0613',
                'gpt-4-0314',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 30_000_000,
                    'output_usd_micros_per_million_tokens' => 60_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 15_000_000,
                    'output_usd_micros_per_million_tokens' => 30_000_000,
                ],
            ],
        ],
        'gpt-3.5-turbo' => [
            'aliases' => [
                'gpt-3.5-turbo-0125',
                'gpt-3.5-turbo-1106',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 500_000,
                    'output_usd_micros_per_million_tokens' => 1_500_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 250_000,
                    'output_usd_micros_per_million_tokens' => 750_000,
                ],
            ],
        ],
        'gpt-3.5-turbo-instruct' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_500_000,
                    'output_usd_micros_per_million_tokens' => 2_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 750_000,
                    'output_usd_micros_per_million_tokens' => 1_000_000,
                ],
            ],
        ],
    ],
];
