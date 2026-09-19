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
    | Sinau Jowo — AI Speech vendors (setup-tts-stt-live.md)
    |--------------------------------------------------------------------------
    | TTS memakai edge-tts (tanpa API key) & STT memakai ElevenLabs Scribe.
    | Bila kredensial STT kosong / edge-tts gagal, service otomatis memakai
    | mode mock deterministik agar demo tetap berjalan (PRD §9 mitigasi).
    */

    'elevenlabs' => [
        'api_key' => env('ELEVENLABS_API_KEY'),
        'stt_model' => env('ELEVENLABS_STT_MODEL', 'scribe_v2'),
        'language' => env('ELEVENLABS_STT_LANGUAGE', 'jav'),
    ],

    'edge_tts' => [
        'binary' => env('EDGE_TTS_BINARY', 'edge-tts'),
        'voice_default' => env('EDGE_TTS_VOICE', 'jv-ID-DimasNeural'),
        'voice_dimas' => env('EDGE_TTS_VOICE_DIMAS', 'jv-ID-DimasNeural'),
        'voice_siti' => env('EDGE_TTS_VOICE_SITI', 'jv-ID-SitiNeural'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'live_translate_model' => env('GEMINI_LIVE_MODEL', 'gemini-3.5-live-translate-preview'),
    ],

];
