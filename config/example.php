<?php
return [
    'env' => 'test', // production requires a real provider and institution consent text
    'dsn' => 'mysql:host=localhost;dbname=vakalab_test;charset=utf8mb4',
    'db_user' => 'vakalab', 'db_password' => '',
    'base_path' => '', // e.g. /vakalab, no trailing slash
    'timezone' => 'Europe/Istanbul', 'https' => true,
    'ai' => ['provider'=>'mock', 'model'=>'synthetic-v1', 'mode'=>'normal',
        'endpoint'=>'https://api.openai.com/v1/chat/completions', 'key'=>'', 'timeout'=>45,
        'structured_outputs'=>false], // Enable only for providers/models supporting strict JSON Schema.
    'autosave_ms'=>1200, 'heartbeat_ms'=>15000, 'idle_ms'=>60000,
    'consent_text'=>'Kurum tarafından onaylanmış onam metni henüz sağlanmadı. Bu ortam sentetik test içindir.',
];
