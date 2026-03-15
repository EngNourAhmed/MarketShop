<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'card_type',
        'card_holder',
        'cvv',
        'expiry_date',
        'type',
        'status',
        'distribution',
        'balance',
        'customer_id',
        'points',
        'points_used',
        'points_remaining',
        'points_spent',
        'points_spent_only',
        'points_spent_on',
        'amount',
        'price_in_eg',
        'price_in_us',
        'price_in_uk',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}