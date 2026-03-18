<?php

return [
    // Available locales. Key = locale code. Add new entries here to expose them
    // everywhere: header language selector, profile default-language dropdown,
    // middleware validation, and the admin translation panel.
    'locales' => [
        'en' => ['label' => 'English',    'flag' => '🇬🇧'],
        'el' => ['label' => 'Ελληνικά',  'flag' => '🇬🇷'],
        'tr' => ['label' => 'Türkçe',     'flag' => '🇹🇷'],
        'de' => ['label' => 'Deutsch',    'flag' => '🇩🇪'],
        'fr' => ['label' => 'Français',   'flag' => '🇫🇷'],
        'es' => ['label' => 'Español',    'flag' => '🇪🇸'],
    ],

    // Media library configuration
    'media' => [
        'disk' => env('AGROFLUX_MEDIA_DISK', 'public'),
        'visibility' => 'public',
        'max_upload_mb' => (int) env('AGROFLUX_MEDIA_MAX_MB', 8),
        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    ],
];
