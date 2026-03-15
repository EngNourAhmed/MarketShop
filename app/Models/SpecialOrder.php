<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;

class SpecialOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'product_name',
        'quantity',
        'color',
        'size',
        'material',
        'specs',
        'reference_url',
        'images',
        'details',
        'budget',
        'status',
        'admin_status',
        'admin_rejection_reason',
        'admin_reviewed_at',
        'factory_status',
        'factory_updated_at',
        'rejection_reason',
        'reviewed_at',
        'supplier_id',
        'product_id',
        'assigned_price',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'admin_reviewed_at' => 'datetime',
        'factory_updated_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'budget' => 'decimal:2',
        'assigned_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
