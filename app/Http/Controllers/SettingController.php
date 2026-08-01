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
            'telegram_token',
            'default_tax_rate',
            'tax_label',
            'tax_number',
            'exchange_rate',
            'card_processing_fee',
            'google_drive_folder_id',
            'google_drive_service_account_json'
        ]);

        if ($request->filled('google_drive_service_account_json')) {
            $json = json_decode($request->google_drive_service_account_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', __('app.invalid_google_json') ?? 'Google Service Account JSON key is not a valid JSON.');
            }
        }

        $data['tax_enabled'] = $request->has('tax_enabled') ? '1' : '0';
        $data['google_drive_backup_enabled'] = $request->has('google_drive_backup_enabled') ? '1' : '0';

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
            'company_bank_qr_payload' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'card_processing_fee' => 'nullable|numeric|min:0|max:100',
            'telegram_token' => 'nullable|string|max:255',
            'wing_pay_merchant_id' => 'nullable|string|max:255',
            'wing_pay_secret_key' => 'nullable|string|max:255',
            'wing_pay_api_url' => 'nullable|url|max:255',
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

            // Try to auto-decode the QR payload from image using API
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->attach(
                    'file', 
                    file_get_contents($request->file('bank_qr')->getRealPath()), 
                    $request->file('bank_qr')->getClientOriginalName()
                )->post('https://api.qrserver.com/v1/read-qr-code/');

                if ($response->successful()) {
                    $result = $response->json();
                    $qrText = $result[0]['symbol'][0]['data'] ?? null;
                    $errorText = $result[0]['symbol'][0]['error'] ?? null;

                    if ($qrText && !$errorText) {
                        Setting::updateOrCreate(['key' => 'company_bank_qr_payload'], ['value' => $qrText]);
                        $validated['company_bank_qr_payload'] = $qrText;
                    } else {
                        session()->flash('warning', app()->getLocale() === 'km' 
                            ? 'រូបភាព QR Code ត្រូវបានរក្សាទុក ប៉ុន្តែប្រព័ន្ធមិនអាចអានអត្ថបទ KHQR ស្វ័យប្រវត្តិបានទេ។ សូមចម្លង (Paste) អត្ថបទ KHQR Payload ដោយដៃក្នុងប្រអប់ខាងក្រោម។'
                            : 'QR Code image saved, but system could not auto-read KHQR text payload. Please paste the KHQR text payload manually below.');
                    }
                }
            } catch (\Throwable $e) {
                session()->flash('warning', app()->getLocale() === 'km' 
                    ? 'រូបភាព QR Code ត្រូវបានរក្សាទុក ប៉ុន្តែប្រព័ន្ធមិនអាចអានអត្ថបទ KHQR ស្វ័យប្រវត្តិបានទេ។ សូមចម្លង (Paste) អត្ថបទ KHQR Payload ដោយដៃក្នុងប្រអប់ខាងក្រោម។'
                    : 'QR Code image saved, but system could not auto-read KHQR text payload. Please paste the KHQR text payload manually below.');
            }
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
            'company_bank_qr_payload' => $validated['company_bank_qr_payload'] ?? '',
            'exchange_rate' => $validated['exchange_rate'] ?? '4100',
            'card_processing_fee' => $validated['card_processing_fee'] ?? '2',
            'telegram_token' => $validated['telegram_token'] ?? '',
            'wing_pay_merchant_id' => $validated['wing_pay_merchant_id'] ?? '',
            'wing_pay_secret_key' => $validated['wing_pay_secret_key'] ?? '',
            'wing_pay_api_url' => $validated['wing_pay_api_url'] ?? '',
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', __('app.company_settings_updated'));
    }

    /**
     * Update a single QR code image and KHQR payload text for a payment method.
     * Key examples: qr_aba, qr_acleda, qr_wing, qr_truemoney, qr_bakong
     */
    public function updateQrSetting(Request $request, string $key)
    {
        if (!str_starts_with($key, 'qr_')) {
            return redirect()->back()->with('error', 'Invalid QR key.');
        }

        $request->validate([
            'qr_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'qr_payload' => 'nullable|string',
        ]);

        // Handle Image Upload if provided
        if ($request->hasFile('qr_image')) {
            // Delete old image if exists
            $old = Setting::where('key', $key)->first();
            if ($old && $old->value && Storage::disk('public')->exists($old->value)) {
                Storage::disk('public')->delete($old->value);
            }

            // Store new image
            $path = $request->file('qr_image')->store('qr', 'public');
            Setting::updateOrCreate(['key' => $key], ['value' => $path]);

            // If key is qr_bakong, keep legacy company_bank_qr in sync
            if ($key === 'qr_bakong') {
                Setting::updateOrCreate(['key' => 'company_bank_qr'], ['value' => $path]);
            }
        }

        // Handle Payload Text
        $payload = $request->input('qr_payload', '');
        Setting::updateOrCreate(['key' => $key . '_payload'], ['value' => $payload]);

        // If key is qr_bakong, keep legacy company_bank_qr_payload in sync
        if ($key === 'qr_bakong') {
            Setting::updateOrCreate(['key' => 'company_bank_qr_payload'], ['value' => $payload]);
        }

        return redirect()->back()->with('success', 'QR Code និង KHQR Payload បានរក្សាទុកដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Delete a QR code image and its payload text for a payment method.
     */
    public function deleteQrSetting(string $key)
    {
        $allowedKeys = ['qr_aba', 'qr_acleda', 'qr_wing', 'qr_truemoney', 'qr_bakong'];

        if (!in_array($key, $allowedKeys)) {
            return redirect()->back()->with('error', 'Invalid QR key.');
        }

        $setting = Setting::where('key', $key)->first();
        if ($setting && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        Setting::where('key', $key)->delete();
        Setting::where('key', $key . '_payload')->delete();

        if ($key === 'qr_bakong') {
            $oldBakong = Setting::where('key', 'company_bank_qr')->first();
            if ($oldBakong && $oldBakong->value && Storage::disk('public')->exists($oldBakong->value)) {
                Storage::disk('public')->delete($oldBakong->value);
            }
            Setting::where('key', 'company_bank_qr')->delete();
            Setting::where('key', 'company_bank_qr_payload')->delete();
        }

        return redirect()->back()->with('success', 'QR Code និង Payload បានលុបដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Add a custom bank QR code.
     */
    public function addCustomQr(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'qr_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ]);

        $path = $request->file('qr_image')->store('qr', 'public');
        $key = 'qr_custom_' . time() . '_' . rand(100, 999);

        // Save image setting key
        Setting::updateOrCreate(['key' => $key], ['value' => $path]);

        $payload = $request->input('qr_payload', '');
        if (!empty($payload)) {
            Setting::updateOrCreate(['key' => $key . '_payload'], ['value' => $payload]);
        }

        // Save metadata into custom_qr_list JSON
        $customListSetting = Setting::where('key', 'custom_qr_list')->first();
        $customList = $customListSetting ? json_decode($customListSetting->value, true) : [];
        if (!is_array($customList)) {
            $customList = [];
        }

        $customList[] = [
            'key' => $key,
            'label' => trim($request->bank_name),
            'icon' => '🏦',
        ];

        Setting::updateOrCreate(['key' => 'custom_qr_list'], ['value' => json_encode($customList)]);

        // Also ensure PaymentMethod exists so it shows in checkout choices
        \App\Models\PaymentMethod::firstOrCreate(['name' => trim($request->bank_name)]);

        return redirect()->back()->with('success', 'បានបន្ថែម QR Code ធនាគារ ' . $request->bank_name . ' ដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Delete a custom bank QR code.
     */
    public function deleteCustomQr(string $key)
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }
        Setting::where('key', $key)->delete();

        // Update custom_qr_list JSON
        $customListSetting = Setting::where('key', 'custom_qr_list')->first();
        if ($customListSetting && $customListSetting->value) {
            $customList = json_decode($customListSetting->value, true);
            if (is_array($customList)) {
                $customList = array_values(array_filter($customList, function ($item) use ($key) {
                    return isset($item['key']) && $item['key'] !== $key;
                }));
                Setting::updateOrCreate(['key' => 'custom_qr_list'], ['value' => json_encode($customList)]);
            }
        }

        return redirect()->back()->with('success', 'បានលុប QR Code ធនាគារដោយជោគជ័យ!')->with('qr_tab', true);
    }
}

