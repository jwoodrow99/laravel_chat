<?php

namespace jwoodrow99\laravel_chat\Traits;

use jwoodrow99\laravel_chat\Models\Message;

trait UserHasMessages
{
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
