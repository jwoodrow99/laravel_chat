<?php

namespace jwoodrow99\laravel_chat;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware(config('laravel-chat.route.middleware'))->prefix(config('laravel-chat.route.prefix'))->group( function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/laravle_chat_routes.php');
        });

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-chat.php'),
            ], 'config');

            if (!class_exists('CreateChatsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_chats_table.php.stub' => database_path(config('laravel-chat.migration_dir') . '/' . date('Y_m_d_His', time()+1) . '_create_chats_table.php'),
                ], 'migrations');
            }

            if (!class_exists('CreateMessagesTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_messages_table.php.stub' => database_path(config('laravel-chat.migration_dir') . '/' . date('Y_m_d_His', time()+2) . '_create_messages_table.php'),
                ], 'migrations');
            }

            if (!class_exists('AddUsersToChatsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/add_users_to_chats_table.php.stub' => database_path(config('laravel-chat.migration_dir') . '/' . date('Y_m_d_His', time()+3) . '_add_users_to_chats_table.php'),
                ], 'migrations');
            }
        }
    }
}
