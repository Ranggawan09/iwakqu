<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'total_price',
        'shipping_cost',
        'distance_km',
        'status',
        'payment_method',
        'transaction_id',
        'snap_token',
        'payment_link',
        'mayar_id',
        'voucher_code',
        'voucher_discount',
        'global_discount_amount',
    ];

    protected $casts = [
        'total_price' => 'decimal:0',
        'shipping_cost' => 'decimal:0',
        'voucher_discount' => 'decimal:0',
        'global_discount_amount' => 'decimal:0',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function rating()
    {
        return $this->hasOne(OrderRating::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
                'menunggu_pembayaran' => 'Menunggu Pembayaran',
                'dibayar' => 'Dibayar',
                'diproses' => 'Diproses',
                'dikirim' => 'Sedang Dikirim',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                default => ucfirst($this->status),
            };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
                'menunggu_pembayaran' => 'yellow',
                'dibayar' => 'blue',
                'diproses' => 'indigo',
                'dikirim' => 'orange',
                'selesai' => 'green',
                'dibatalkan' => 'red',
                default => 'gray',
            };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}
