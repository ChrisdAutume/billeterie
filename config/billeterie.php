<?php

return [
    'don' => [
        'enabled' => env('DON_ENABLED', false),
        'name' => 'Don de promo',
        'text' => "Cette année pour le don de promo 2018, contribuez à l'amélioration de la vie à l'UTT en participant !",
        'min'  =>   1000,
    ],
    'billet' =>[
    'text_color' => env('BILLET_TEXT_COLOR', '#000000')
    ],
    'contact' => env('APP_CONTACT', 'test@test.fr'),
    'event' => [
        'name' => env('APP_NAME', 'Test'),
        'subname' => env('APP_SUBNAME', 'Test'),
    ],
    'piwik' => env('PIWIK_SITE_ID', null),
    'analytics' => env('GOOGLE_ANALYTICS_ID', null),
    'landing_until' => env('LANDING_DATE', '2017/11/01 14:00'),

];