<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate their service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the CVision AI Engine (FastAPI backend).
    |
    */
    'ai' => [
        'engine_url'  => env('AI_ENGINE_URL', 'http://127.0.0.1:8000'),
        'timeout'     => env('AI_ENGINE_TIMEOUT', 120),
        'python_path' => env('PYTHON_PATH', base_path('venv/Scripts/python.exe')),
    ],

];