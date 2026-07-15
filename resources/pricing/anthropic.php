<?php

declare(strict_types=1);

return [
    'provider' => 'anthropic',
    'name' => 'anthropic',
    'as_of' => '2026-07-15',
    'source_urls' => [
        'https://platform.claude.com/docs/en/about-claude/pricing',
        'https://platform.claude.com/docs/en/about-claude/models/overview',
    ],
    'models' => [
        'claude-fable-5' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'input_usd_microcent_per_million_tokens' => 1_000_000,
                    'cached_input_usd_microcent_per_million_tokens' => 100_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 1_250_000,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 2_000_000,
                    'output_usd_microcent_per_million_tokens' => 5_000_000,
                ],
                'batch' => [
                    'input_usd_microcent_per_million_tokens' => 500_000,
                    'cached_input_usd_microcent_per_million_tokens' => 50_000,
                    'cache_write_5m_input_usd_microcent_per_million_tokens' => 625_000,
                    'cache_write_1h_input_usd_microcent_per_million_tokens' => 1_000_000,
                    'output_usd_microcent_per_million_tokens' => 2_500_000,
                ],
            ],
        ],
        'claude-opus-4-8' => [
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
        'claude-sonnet-5' => [
            'aliases' => [],
            'cards' => [
                'standard' => [
                    'periods' => [
                        [
                            'effective_until' => '2026-08-31',
                            'prices' => [
                                'input_usd_microcent_per_million_tokens' => 200_000,
                                'cached_input_usd_microcent_per_million_tokens' => 20_000,
                                'cache_write_5m_input_usd_microcent_per_million_tokens' => 250_000,
                                'cache_write_1h_input_usd_microcent_per_million_tokens' => 400_000,
                                'output_usd_microcent_per_million_tokens' => 1_000_000,
                            ],
                        ],
                        [
                            'effective_from' => '2026-09-01',
                            'prices' => [
                                'input_usd_microcent_per_million_tokens' => 300_000,
                                'cached_input_usd_microcent_per_million_tokens' => 30_000,
                                'cache_write_5m_input_usd_microcent_per_million_tokens' => 375_000,
                                'cache_write_1h_input_usd_microcent_per_million_tokens' => 600_000,
                                'output_usd_microcent_per_million_tokens' => 1_500_000,
                            ],
                        ],
                    ],
                ],
                'batch' => [
                    'periods' => [
                        [
                            'effective_until' => '2026-08-31',
                            'prices' => [
                                'input_usd_microcent_per_million_tokens' => 100_000,
                                'cached_input_usd_microcent_per_million_tokens' => 10_000,
                                'cache_write_5m_input_usd_microcent_per_million_tokens' => 125_000,
                                'cache_write_1h_input_usd_microcent_per_million_tokens' => 200_000,
                                'output_usd_microcent_per_million_tokens' => 500_000,
                            ],
                        ],
                        [
                            'effective_from' => '2026-09-01',
                            'prices' => [
                                'input_usd_microcent_per_million_tokens' => 150_000,
                                'cached_input_usd_microcent_per_million_tokens' => 15_000,
                                'cache_write_5m_input_usd_microcent_per_million_tokens' => 187_500,
                                'cache_write_1h_input_usd_microcent_per_million_tokens' => 300_000,
                                'output_usd_microcent_per_million_tokens' => 750_000,
                            ],
                        ],
                    ],
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
