<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Credentials for external services. All values MUST come from the
    | environment via env() so that no secrets are baked into source code.
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
    | Gemini AI
    |--------------------------------------------------------------------------
    |
    | The Gemini integration is configured entirely through environment
    | variables. The API key is read from config/services.php at runtime
    | and is never committed to source control.
    |
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/'),
        'model' => env('GEMINI_MODEL', 'gemini-flash-latest'),
        'connect_timeout' => (int) env('GEMINI_CONNECT_TIMEOUT', 5),
        'request_timeout' => (int) env('GEMINI_REQUEST_TIMEOUT', 120),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 1024),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.7),
        'max_retries' => (int) env('GEMINI_MAX_RETRIES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq AI (fallback provider)
    |--------------------------------------------------------------------------
    |
    | Used when the primary Gemini provider is unavailable. Configured through
    | environment variables only.
    */

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'connect_timeout' => (int) env('GROQ_CONNECT_TIMEOUT', 5),
        'request_timeout' => (int) env('GROQ_REQUEST_TIMEOUT', 120),
        'max_output_tokens' => (int) env('GROQ_MAX_OUTPUT_TOKENS', 1024),
        'temperature' => (float) env('GROQ_TEMPERATURE', 0.7),
        'max_retries' => (int) env('GROQ_MAX_RETRIES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat rate limiting
    |--------------------------------------------------------------------------
    |
    | Per-IP throttle applied by App\Http\Middleware\RateLimitChat.
    |
    */

    'chat' => [
        'rate_limit_per_minute' => (int) env('CHAT_RATE_LIMIT_PER_MINUTE', 30),
    ],
];