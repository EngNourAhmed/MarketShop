<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['key', 'name'];

    // Permission.php
    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}
