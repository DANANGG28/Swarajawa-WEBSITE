<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RAG Chatbot AI Provider (OpenAI-compatible custom provider/proxy)
    |--------------------------------------------------------------------------
    |
    | Semua nilai diambil dari .env agar tidak ada API key yang ter-hardcode
    | maupun ter-commit ke repositori. Base URL default sengaja dibiarkan
    | null supaya tidak mengunci ke localhost — gampang diganti per
    | environment (mis. hostname container saat deploy).
    |
    */

    'base_url' => env('AI_PROVIDER_BASE_URL'),

    'api_key' => env('AI_PROVIDER_API_KEY'),

    'model' => env('AI_PROVIDER_MODEL', 'gemini-3.8-flash'),

    'timeout' => (int) env('AI_PROVIDER_TIMEOUT', 30),
];
