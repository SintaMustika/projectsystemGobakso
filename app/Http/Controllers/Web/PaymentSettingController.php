<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $setting = PaymentSetting::first();

        return view('payment-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'qris_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $setting = PaymentSetting::firstOrNew(['id' => 1]);

        if ($request->hasFile('qris_image')) {
            if ($setting->qris_image) {
                Storage::disk('public')->delete($setting->qris_image);
            }

            $extension = $request->file('qris_image')->getClientOriginalExtension();
            $data['qris_image'] = $request->file('qris_image')->storeAs('qris', 'qris.' . $extension, 'public');
        }

        $setting->fill([
            'qris_image' => $data['qris_image'],
        ])->save();

        return redirect()->route('admin.payment-settings.edit')->with('success', 'QRIS berhasil disimpan');
    }
}
