<?php

namespace jwoodrow99\laravel_chat\Traits;

use jwoodrow99\laravel_chat\Models\Chat;

trait UserHasChats
{
    public function chats()
    {
        return $this->belongsToMany(Chat::class)->withPivot('new_messages');
    }
}
