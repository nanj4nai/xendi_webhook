<?php
// config.php
return [
    'xendit_api_key' => getenv('xendit_api_key'),
    'xendit_webhook_token' => getenv('xendit_webhook_token'),

    'sendgrid_api_key' => getenv('sendgrid_api_key'),
    'email_from' => 'nandesuu@gmail.com',
    'email_from_name' => 'Villa Rosal Beach Resort',

    'supabase_key' => getenv('supabase_key'),
    'supabase_url' => getenv('supabase_url'),
];
