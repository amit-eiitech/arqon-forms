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
        'signed_url_expiration_minutes' => 30,
        'allow_submissions' => false,
    ],

    'views' => [
        'namespace' => 'arqon-forms',
    ],

    'render' => [
        'layout' => null,
        'section' => 'content',
        'container_class' => null,
        'show_header' => true,
        'show_footer' => false,
        'footer_text' => null,
        'theme' => [
            'primary_color' => '#111827',
            'background_color' => '#f5f5f5',
            'card_background_color' => '#ffffff',
            'text_color' => '#222222',
            'radius' => '12px',
        ],
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
        'queue' => [
            'connection' => env('ARQON_FORMS_NOTIFICATION_QUEUE_CONNECTION'),
            'queue' => env('ARQON_FORMS_NOTIFICATION_QUEUE'),
            'delay_seconds' => (int) env('ARQON_FORMS_NOTIFICATION_QUEUE_DELAY_SECONDS', 0),
        ],
    ],

    'filament' => [
        'navigation_group' => 'Forms',
        'forms_navigation_label' => 'Forms',
        'submissions_navigation_label' => 'Submissions',
    ],
];
