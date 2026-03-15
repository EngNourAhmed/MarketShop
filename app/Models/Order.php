<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\OrderReturn;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'invoice_id',
        'shipping_id',
        'order_code',
        'payment_code',
        'shipping_code',
        'added_by',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'shipping_method',
        'shipping_status',
        'note',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }
}