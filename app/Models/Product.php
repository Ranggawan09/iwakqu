<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('images/products/' . $this->image))) {
            return asset('images/products/' . $this->image);
        }
        return asset('images/products/default.jpg');
    }
}
