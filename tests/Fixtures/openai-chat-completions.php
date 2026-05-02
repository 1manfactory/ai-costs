<?php

declare(strict_types=1);

return [
    'id' => 'chatcmpl_example',
    'object' => 'chat.completion',
    'model' => 'gpt-5.5',
    'service_tier' => 'default',
    'usage' => [
        'prompt_tokens' => 1500,
        'prompt_tokens_details' => [
            'cached_tokens' => 500,
        ],
        'completion_tokens' => 600,
        'completion_tokens_details' => [
            'reasoning_tokens' => 120,
        ],
    ],
];
