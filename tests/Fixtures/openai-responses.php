<?php

declare(strict_types=1);

return [
    'id' => 'resp_example',
    'object' => 'response',
    'model' => 'gpt-5.4',
    'service_tier' => 'default',
    'usage' => [
        'input_tokens' => 1200,
        'input_tokens_details' => [
            'cached_tokens' => 200,
        ],
        'output_tokens' => 300,
        'output_tokens_details' => [
            'reasoning_tokens' => 50,
        ],
    ],
];
