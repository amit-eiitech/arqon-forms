<?php

use Filament\Support\Icons\Heroicon;

return [
    'route' => [
        'enabled' => true,
        'prefix' => 'forms',
        'middleware' => ['web'],
        'name_prefix' => 'arqon-forms.',
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

    /*
    |--------------------------------------------------------------------------
    | Package Icons
    |--------------------------------------------------------------------------
    |
    | Customize navigation and action icons used across Filament resources.
    | Values can be Heroicon enums (e.g. Heroicon::OutlinedDocumentText)
    | or icon string identifiers (e.g. 'heroicon-o-document-text').
    | When not configured, the package defaults from HasPackageIcons are used.
    | When set to null, the icons will not be used.
    | Check all available options in README.md file.
    |
    */
    'icons' => [
        // Navigation Icons
        // 'form' => Heroicon::OutlinedDocumentText,
        // 'submissions' => Heroicon::OutlinedInbox,
    ],
];
