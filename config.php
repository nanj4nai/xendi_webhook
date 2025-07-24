<?php
return [
    'xendit_api_key' => getenv('XENDIT_API_KEY'),
    'xendit_webhook_token' => getenv('XENDIT_WEBHOOK_TOKEN'),
    'sendgrid_api_key' => getenv('SENDGRID_API_KEY'),
    'email_from' => getenv('EMAIL_FROM'),
    'email_from_name' => getenv('EMAIL_FROM_NAME'),
    'supabase_key' => getenv('SUPABASE_KEY'),
    'supabase_url' => getenv('SUPABASE_URL'),
];
