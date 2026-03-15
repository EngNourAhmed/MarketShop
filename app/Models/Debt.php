<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'description',
        'type',
        'amount',
        'commission_percent',
        'commission_amount',
        'due_date',
        'status',
    ];


    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}