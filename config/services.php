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
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sinau Jowo — AI Speech & RAG vendors (PRD §6C, §10.2)
    |--------------------------------------------------------------------------
    | Semua kredensial opsional. Bila kosong, service terkait otomatis
    | memakai mode mock deterministik agar demo tetap berjalan tanpa key.
    */

    'azure_speech' => [
        'key' => env('AZURE_SPEECH_KEY'),
        'region' => env('AZURE_SPEECH_REGION', 'southeastasia'),
        'voice_default' => env('AZURE_SPEECH_VOICE', 'jv-ID-SitiNeural'),
        'voice_male' => env('AZURE_SPEECH_VOICE_MALE', 'jv-ID-DimasNeural'),
    ],

    'google_speech' => [
        'key' => env('GOOGLE_SPEECH_KEY'),
        'language' => env('GOOGLE_SPEECH_LANGUAGE', 'jv-ID'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'live_translate_model' => env('GEMINI_LIVE_MODEL', 'gemini-3.5-live-translate-preview'),
    ],

];
