<?php

return [
    'don' => [
        'enabled' => false,
        'name' => 'Don de promo',
        'text' => "Ici une description",
    ],
    'contact' => env('APP_CONTACT', 'test@test.fr'),
    'event' => [
        'name' => env('APP_NAME', 'Test'),
        'subname' => env('APP_SUBNAME', 'Test'),
    ],
    'piwik' => env('PIWIK_SITE_ID', null)

];