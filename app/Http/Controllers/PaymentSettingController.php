<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $setting = PaymentSetting::first();
        return view('admin.payment_setting', ['setting' => $setting]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|max:5120',
        ]);

        $setting = PaymentSetting::first();

        $path = $request->file('qris_image')->store('qris', 'public');

        if ($setting) {
            // delete old
            if ($setting->qris_image && Storage::disk('public')->exists($setting->qris_image)) {
                Storage::disk('public')->delete($setting->qris_image);
            }
            $setting->qris_image = $path;
            $setting->save();
        } else {
            $setting = PaymentSetting::create(['qris_image' => $path]);
        }

        return redirect()->back()->with('success', 'QRIS image uploaded.');
    }

    // Public API for customers
    public function apiCustomer()
    {
        $setting = PaymentSetting::first();

        if (! $setting || ! $setting->qris_image) {
            return response()->json(['qris_url' => null]);
        }

        $url = url(Storage::url($setting->qris_image));

        return response()->json(['qris_url' => $url]);
    }
}
