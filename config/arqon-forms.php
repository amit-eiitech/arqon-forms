<?php

return [
    'route' => [
        'enabled' => true,
        'prefix' => 'forms',
        'middleware' => ['web'],
        'name_prefix' => 'arqon-forms.',
    ],

    'preview' => [
        'enabled' => true,
        'path' => '_preview',
        'allow_submissions' => false,
    ],

    'render' => [
        'layout' => null,
        'section' => 'content',
        'footer_text' => null,
    ],

    'submission' => [
        'rate_limit' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
        'store_ip_hash' => true,
        'ip_hash_key' => env('ARQON_FORMS_IP_HASH_KEY'),
    ],

    'notifications' => [
        'delivery' => env('ARQON_FORMS_NOTIFICATION_DELIVERY', 'sync'), // sync | queue
    ],

    'filament' => [
        'navigation_group' => 'Forms',
        'forms_navigation_label' => 'Forms',
        'submissions_navigation_label' => 'Submissions',
    ],
];
