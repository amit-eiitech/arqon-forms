<?php

return [
    'route' => [
        'enabled' => true,
        'prefix' => 'forms',
        'middleware' => ['web'],
        'name_prefix' => 'arqon-forms.',
    ],

    'views' => [
        'namespace' => 'arqon-forms',
    ],

    'submission' => [
        'rate_limit' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
        'store_ip_hash' => true,
        'ip_hash_key' => env('ARQON_FORMS_IP_HASH_KEY'),
    ],


    'filament' => [
        'navigation_group' => 'Forms',
        'forms_navigation_label' => 'Forms',
        'submissions_navigation_label' => 'Submissions',
    ],
];
