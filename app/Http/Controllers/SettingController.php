<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'shop_name', 
            'shop_address', 
            'shop_phone', 
            'shop_email', 
            'currency', 
            'default_interest_rate', 
            'telegram_token',
            'default_tax_rate',
            'tax_label',
            'tax_number',
            'exchange_rate'
        ]);

        $data['tax_enabled'] = $request->has('tax_enabled') ? '1' : '0';

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Settings updated.');
    }

    public function updateCompanySettings(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_name_km' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'address_km' => 'required|string|max:500',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'business_license' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'bank_qr' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'exchange_rate' => 'nullable|numeric',
            'company_aba_pay_link' => 'nullable|url',
            'currency' => 'nullable|string|max:10',
            'default_interest_rate' => 'nullable|numeric',
            'telegram_token' => 'nullable|string|max:255',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'company_logo')->first();
            if ($oldLogo && $oldLogo->value && Storage::disk('public')->exists($oldLogo->value)) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('company', 'public');
            Setting::updateOrCreate(['key' => 'company_logo'], ['value' => $logoPath]);
        }

        // Handle Bank QR upload
        if ($request->hasFile('bank_qr')) {
            // Delete old Bank QR if exists
            $oldQr = Setting::where('key', 'company_bank_qr')->first();
            if ($oldQr && $oldQr->value && Storage::disk('public')->exists($oldQr->value)) {
                Storage::disk('public')->delete($oldQr->value);
            }

            // Store new Bank QR
            $qrPath = $request->file('bank_qr')->store('company', 'public');
            Setting::updateOrCreate(['key' => 'company_bank_qr'], ['value' => $qrPath]);
        }

        // Update company settings
        $settingsData = [
            'company_name' => $validated['company_name'],
            'company_name_km' => $validated['company_name_km'],
            'company_address' => $validated['address'],
            'company_address_km' => $validated['address_km'],
            'company_phone' => $validated['phone'],
            'company_email' => $validated['email'],
            'company_business_license' => $validated['business_license'] ?? '',
            'exchange_rate' => $validated['exchange_rate'] ?? '4100',
            'company_aba_pay_link' => $validated['company_aba_pay_link'] ?? '',
            'currency' => $validated['currency'] ?? 'USD',
            'default_interest_rate' => $validated['default_interest_rate'] ?? '0',
            'telegram_token' => $validated['telegram_token'] ?? '',
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', __('app.company_settings_updated'));
    }
}
