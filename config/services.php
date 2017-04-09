<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'etupay' => [
        'api_key' => env('ETUPAY_APIKEY'),
        'endpoint' => env('ETUPAY_ENDPOINT').'?service_id='.env('ETUPAY_SERVICEID'),
        'service_id' => env('ETUPAY_SERVICEID'),
    ],

    'etuutt'=> [
        'client' => [
            'id' => env('ETUUTT_CLIENT_ID'),
            'secret' => env('ETUUTT_CLIENT_SECRET'),
        ],
        'baseuri' => [
            'api'    => env('ETUUTT_BASEURI_API', 'https://etu.utt.fr'),
            'public' => env('ETUUTT_BASEURI_PUBLIC', 'https://etu.utt.fr'),
        ]
    ],

];
