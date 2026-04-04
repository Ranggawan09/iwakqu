<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'target',
        'value',
        'min_purchase',
        'max_discount',
        'is_active',
        'is_single_use',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:0',
        'min_purchase' => 'decimal:0',
        'max_discount' => 'decimal:0',
        'is_active' => 'boolean',
        'is_single_use' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isValid(?float $subtotal = null, ?User $user = null): bool
    {
        if (!$this->is_active) return false;

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) return false;

        if ($subtotal !== null && $subtotal < $this->min_purchase) return false;

        if ($this->is_single_use && $user) {
            $used = VoucherUsage::where('user_id', $user->id)
                ->where('voucher_id', $this->id)
                ->exists();
            if ($used) return false;
        }

        return true;
    }

    public function calculateDiscount(float $targetAmount): float
    {
        if ($this->type === 'percent') {
            $discount = $targetAmount * ($this->value / 100);
            if ($this->max_discount > 0) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }

        return min($this->value, $targetAmount);
    }
}
