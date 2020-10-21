<?php

return [
    'table' => [
        'chat' => env('LARAVEL_CHAT_CHAT_TABLE', 'chats'),
        'message' => env('LARAVEL_CHAT_MESSAGE_TABLE', 'messages'),
        'chatUser' => env('LARAVEL_CHAT_CHAT_USER_TABLE', 'chat_user')
    ],
    'route' => [
        'prefix' => env('LARAVEL_CHAT_ROUTE_PREFIX', 'chat'),
        'middleware' => env('LARAVEL_CHAT_ROUTE_MIDDLEWARE', 'auth:web'),
        'privileged_middleware' => env('LARAVEL_CHAT_ROUTE_PRIVILEGED_MIDDLEWARE', 'auth:web')
    ],
    'migration_dir' => env('LARAVEL_CHAT_MIGRATION_DIR', 'migrations'),
    'channel-name' => env('LARAVEL_CHAT_CHANNEL_NAME', null),
];
