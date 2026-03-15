<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserAgency extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'status',
        'user_id',
        'description',
        'facebook',
        'twitter',
        'instagram',
        'cost',
        'logo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
