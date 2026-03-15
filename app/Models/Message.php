<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_role',
        'body',
        'is_read_by_user',
        'is_read_by_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
