<?php

declare(strict_types=1);

return [
    'id' => 'msg_example',
    'type' => 'message',
    'model' => 'claude-sonnet-4-6',
    'usage' => [
        'input_tokens' => 1000,
        'cache_read_input_tokens' => 500,
        'cache_creation_input_tokens' => 300,
        'cache_creation' => [
            'ephemeral_5m_input_tokens' => 200,
            'ephemeral_1h_input_tokens' => 100,
        ],
        'output_tokens' => 400,
        'service_tier' => 'standard',
    ],
];
