<?php

use Illuminate\Support\Facades\Route;
use jwoodrow99\laravel_chat\Http\Controllers\Laravel_ChatChatController as ChatController;
use jwoodrow99\laravel_chat\Http\Controllers\Laravel_ChatMessageController as MessageController;

// Chat Routes
Route::get('/', [ChatController::class, 'index']); // Get all chats you have access to
Route::get('/{chat}', [ChatController::class, 'show']); // Get specific chat you have access to
Route::post('/', [ChatController::class, 'create'])->middleware(config('laravel-chat.route.privileged_middleware')); // Create new chat
Route::delete('/{chat}', [ChatController::class, 'destroy'])->middleware(config('laravel-chat.route.privileged_middleware')); // Delete chat
Route::post('/{chat}/read', [ChatController::class, 'read']); // Read specific chat

// Chat User Routes
Route::get('/{chat}/user', [ChatController::class, 'users']); // Get list of users in chat
Route::post('/{chat}/user', [ChatController::class, 'addUsers'])->middleware(config('laravel-chat.route.privileged_middleware')); // Add users to chat
Route::post('/{chat}/user/sync', [ChatController::class, 'syncUsers'])->middleware(config('laravel-chat.route.privileged_middleware')); // Make user list identical to passed values
Route::delete('/{chat}/user/{user}', [ChatController::class, 'removeUser'])->middleware(config('laravel-chat.route.privileged_middleware')); // Remove users from chat

// Chat message routes
Route::get('/{chat}/message', [MessageController::class, 'index']); // Get all messages in chat
Route::post('/{chat}/message', [MessageController::class, 'create']); // Create new chat message
Route::delete('/{chat}/message/{message}', [MessageController::class, 'delete']); // Remove chat message
