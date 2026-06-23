<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'image', 'is_available', 'stock'];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    /**
     * Calculate total material cost for one unit of this menu based on recipes.
     * Returns null when there is no recipe defined.
     */
    public function materialCost()
    {
        $recipes = $this->relationLoaded('recipes') ? $this->recipes : $this->recipes()->with('ingredient')->get();
        if ($recipes->count() === 0) return null;

        $cost = 0.0;
        foreach ($recipes as $r) {
            if (!$r->ingredient) continue;
            $ingredientPrice = isset($r->ingredient->price) ? (float) $r->ingredient->price : 0.0;
            $qty = (float) $r->qty_usage;
            $cost += $qty * $ingredientPrice;
        }

        return round($cost, 2);
    }

    /**
     * Profit per unit = menu price - material cost. Null when no recipe.
     */
    public function profitPerUnit()
    {
        $cost = $this->materialCost();
        if (is_null($cost)) return null;
        return round((float) $this->price - $cost, 2);
    }
}
