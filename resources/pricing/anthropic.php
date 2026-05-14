<?php

declare(strict_types=1);

return [
    'as_of' => '2026-05-13',
    'source' => 'https://platform.claude.com/docs/en/about-claude/pricing',
    'models' => [
        'claude-opus-4-7' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 500_000,
                    'cached_input_usd_microcent_per_million_tokens' => 50_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 625_000,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 1_000_000,
                    'output_usd_microcent_per_million_tokens' => 2_500_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 250_000,
                    'cached_input_usd_microcent_per_million_tokens' => 25_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 312_500,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 500_000,
                    'output_usd_microcent_per_million_tokens' => 1_250_000,
                ],
            ],
        ],
        'claude-sonnet-4-6' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 300_000,
                    'cached_input_usd_microcent_per_million_tokens' => 30_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 375_000,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 600_000,
                    'output_usd_microcent_per_million_tokens' => 1_500_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 150_000,
                    'cached_input_usd_microcent_per_million_tokens' => 15_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 187_500,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 300_000,
                    'output_usd_microcent_per_million_tokens' => 750_000,
                ],
            ],
        ],
        'claude-haiku-4-5-20251001' => [
            'aliases' => [
                'claude-haiku-4-5',
            ],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 100_000,
                    'cached_input_usd_microcent_per_million_tokens' => 10_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 125_000,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 200_000,
                    'output_usd_microcent_per_million_tokens' => 500_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 50_000,
                    'cached_input_usd_microcent_per_million_tokens' => 5_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 62_500,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 100_000,
                    'output_usd_microcent_per_million_tokens' => 250_000,
                ],
            ],
        ],
    ],
];
