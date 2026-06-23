<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['menu_id', 'qty', 'production_date'];

    protected $casts = [
        'production_date' => 'date',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
