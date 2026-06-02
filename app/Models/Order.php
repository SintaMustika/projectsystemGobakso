<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['table_number', 'total_price', 'status'];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Order statuses
    public const STATUS_PENDING = 'pending';
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
        if ($this->status === 'paid') {
            return $this;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            foreach ($this->details as $detail) {
                $recipes = Recipe::where('menu_id', $detail->menu_id)->get();
                foreach ($recipes as $recipe) {
                    $ingredient = Ingredient::lockForUpdate()->find($recipe->ingredient_id);
                    if (! $ingredient) {
                        throw new \Exception('Ingredient not found for recipe');
                    }
                    $deduct = bcmul((string)$detail->qty, (string)$recipe->qty_usage, 3);
                    $newStock = bcsub((string)$ingredient->stock_quantity, (string)$deduct, 3);
                    if (bccomp($newStock, '0', 3) < 0) {
                        throw new \Exception("Insufficient stock for {$ingredient->item_name}");
                    }
                    $ingredient->stock_quantity = $newStock;
                    $ingredient->save();

                    // If stock reaches zero, mark all menus that use this ingredient as unavailable
                    if (bccomp((string)$newStock, '0', 3) === 0) {
                        Menu::whereHas('recipes', function ($q) use ($ingredient) {
                            $q->where('ingredient_id', $ingredient->id);
                        })->update(['is_available' => false]);
                    }
                }
            }

            $this->status = 'paid';
            $this->save();

            return $this;
        });
    }
}
