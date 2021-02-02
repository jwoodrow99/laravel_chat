<?php

namespace jwoodrow99\laravel_chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Message extends Model
{
    // use SoftDeletes;

    protected $table = "messages";

    protected  $fillable = [
        'message',
        'chat_id',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function chat(){
        return $this->belongsTo(Chat::class);
    }
}
