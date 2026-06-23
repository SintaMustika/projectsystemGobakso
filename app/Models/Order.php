<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'table_number',
        'notes',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Pay the order: deduct ingredients stock according to recipes and mark as paid.
     * Throws exception on insufficient stock.
     */
    public function pay()
    {
        if ($this->payment_status === 'paid') {
            return $this;
        }
        // New behavior: deduct menu stock only (menu already produced)
        return \Illuminate\Support\Facades\DB::transaction(function () {
            foreach ($this->details as $detail) {
                $menu = Menu::lockForUpdate()->find($detail->menu_id);
                if (! $menu) {
                    throw new \Exception('Menu not found for order detail');
                }

                $qty = (int) $detail->qty;
                $stockBefore = (int) $menu->stock;
                if ($stockBefore < $qty) {
                    throw new \Exception("Stok menu tidak cukup untuk {$menu->name}. Dibutuhkan: {$qty}, tersedia: {$stockBefore}");
                }

                $menu->stock = $stockBefore - $qty;
                $menu->save();

                if ($menu->stock <= 0) {
                    $menu->is_available = false;
                    $menu->save();
                }
            }

            $this->payment_status = 'paid';
            if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_WAITING, self::STATUS_PAID], true)) {
                $this->status = self::STATUS_PENDING;
            }
            $this->save();

            return $this;
        });
    }
}
