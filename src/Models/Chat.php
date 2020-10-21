<?php

namespace jwoodrow99\laravel_chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "chats";

    protected $fillable = [
        'name'
    ];

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function users(){
        return $this->belongsToMany(User::class)->withPivot('new_messages');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }
}