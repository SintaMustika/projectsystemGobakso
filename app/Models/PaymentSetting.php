<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $table = 'payment_settings';

    protected $fillable = [
        'qris_image',
    ];

    protected $appends = [
        'qris_url',
    ];

    public function getQrisUrlAttribute()
    {
        if (!$this->qris_image) {
            return null;
        }

        return url(Storage::url($this->qris_image));
    }
}