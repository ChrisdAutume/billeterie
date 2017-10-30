<?php

return [
    'don' => [
        'enabled' => true,
        'name' => 'Don de promo',
        'text' => "Ici une description",
        'min'  =>   1000,
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