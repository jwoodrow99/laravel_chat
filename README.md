# laravel-chat

In composer.json file add the following under 'autoload => psr-4'
```
"jwoodrow99\\laravel_chat\\": "packages/jwoodrow99/laravel_chat/src"
```

Add the service provider to the 'config/app.php' file under 'providers'
```
jwoodrow99\laravel_chat\LaravelChatServiceProvider::class
```

Publish providers
``` sh
php artisan vendor:publish --provider="jwoodrow99\laravel_chat\LaravelChatServiceProvider" --tag="config"
php artisan vendor:publish --provider="jwoodrow99\laravel_chat\LaravelChatServiceProvider" --tag="migrations"
```

Add relation traits to User model
```
use jwoodrow99\laravel_chat\Traits\UserHasMessages;
use jwoodrow99\laravel_chat\Traits\UserHasChats;
use jwoodrow99\laravel_chat\Traits\UserOwnedChats;

class User extends Authenticatable 
{
    use UserHasMessages, UserHasChats, UserOwnedChats;

    ...
}
```

When a new message is created, an internal laravel event is broadcast called 'NewMessage'
If you are using a socket server to notify users of messages, it is suggested to make a listener
for the event and then broadcast on the socket from the listener.
