<?php

namespace jwoodrow99\laravel_chat\Traits;

use jwoodrow99\laravel_chat\Models\Chat;

trait UserOwnedChats
{
    public function ownedChats()
    {
        return $this->hasMany(Chat::class);
    }
}
