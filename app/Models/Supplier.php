<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'commission_percent',
        'user_id',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'logo',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'factory_short_details',
        'factory_long_details',
        'created_by',
        'updated_by',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier_prices')
            ->withPivot('price', 'unit_price', 'quantity')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
