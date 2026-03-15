<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Card;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'country_code',
        'added_by',
    ];




    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function notifications()
    {
        return $this->hasMany(CustomerNotification::class);
    }

    public function orderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function latestCard()
    {
        return $this->hasOne(Card::class)->latestOfMany();
    }

    // public function debts()
    // {
    //     return $this->hasMany(Debt::class);
    // }
}