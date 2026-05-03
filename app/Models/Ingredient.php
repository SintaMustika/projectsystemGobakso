<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['item_name', 'stock_quantity', 'unit', 'min_stock', 'price'];

    protected $attributes = [
        'price' => 0,
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:3',
        'min_stock' => 'decimal:3',
        'price' => 'decimal:2',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
