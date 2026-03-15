<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countery extends Model
{
    protected $table = 'counteries';

    protected $fillable = [
        'name',
        'code',
        'flag',
        'currency',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
    ];

    /**
     * Get the exchange rate relative to EGP (primary currency).
     * If rate is 50.0 for USD, it means 1 USD = 50 EGP.
     */
}
