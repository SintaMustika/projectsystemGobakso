<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    public function show()
    {
        $setting = PaymentSetting::first();

        return response()->json([
            'qris_url' => $setting?->qris_url,
        ]);
    }
}
