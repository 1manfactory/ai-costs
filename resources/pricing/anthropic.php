<?php

declare(strict_types=1);

return [
    'as_of' => '2026-05-02',
    'source' => 'https://platform.claude.com/docs/en/about-claude/pricing',
    'models' => [
        'claude-opus-4-7' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 5_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 500_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 6_250_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 10_000_000,
                    'output_usd_micros_per_million_tokens' => 25_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 2_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 250_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 3_125_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 5_000_000,
                    'output_usd_micros_per_million_tokens' => 12_500_000,
                ],
            ],
        ],
        'claude-sonnet-4-6' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 3_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 300_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 3_750_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 6_000_000,
                    'output_usd_micros_per_million_tokens' => 15_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 1_500_000,
                    'cached_input_usd_micros_per_million_tokens' => 150_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 1_875_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 3_000_000,
                    'output_usd_micros_per_million_tokens' => 7_500_000,
                ],
            ],
        ],
        'claude-haiku-4-5-20251001' => [
            'aliases' => [
                'claude-haiku-4-5',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_micros_per_million_tokens' => 1_000_000,
                    'cached_input_usd_micros_per_million_tokens' => 100_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 1_250_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 2_000_000,
                    'output_usd_micros_per_million_tokens' => 5_000_000,
                ],
                'batch' => [
                    'input_usd_micros_per_million_tokens' => 500_000,
                    'cached_input_usd_micros_per_million_tokens' => 50_000,
                    'cache_write_5m_input_usd_micros_per_million_tokens' => 625_000,
                    'cache_write_1h_input_usd_micros_per_million_tokens' => 1_000_000,
                    'output_usd_micros_per_million_tokens' => 2_500_000,
                ],
            ],
        ],
    ],
];
