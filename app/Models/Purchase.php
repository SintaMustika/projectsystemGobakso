<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['invoice', 'supplier', 'total_price', 'purchase_date'];

    protected $casts = [
        'purchase_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
