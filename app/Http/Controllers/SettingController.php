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
        return view('admin.settings.index_clean', compact('settings'));
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
            'card_bank_name' => 'nullable|string|max:255',
            'card_merchant_id' => 'nullable|string|max:255',
            'card_api_key' => 'nullable|string|max:255',
            'card_processing_fee' => 'nullable|numeric|min:0|max:100',
            'telegram_token' => 'nullable|string|max:255',
            'wing_pay_merchant_id' => 'nullable|string|max:255',
            'wing_pay_secret_key' => 'nullable|string|max:255',
            'wing_pay_api_url' => 'nullable|url|max:255',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_label' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
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
            'card_bank_name' => $validated['card_bank_name'] ?? '',
            'card_merchant_id' => $validated['card_merchant_id'] ?? '',
            'card_api_key' => $validated['card_api_key'] ?? '',
            'card_processing_fee' => $validated['card_processing_fee'] ?? '3',
            'telegram_token' => $validated['telegram_token'] ?? '',
            'wing_pay_merchant_id' => $validated['wing_pay_merchant_id'] ?? '',
            'wing_pay_secret_key' => $validated['wing_pay_secret_key'] ?? '',
            'wing_pay_api_url' => $validated['wing_pay_api_url'] ?? '',
            'tax_enabled' => $request->has('tax_enabled') ? '1' : '0',
            'default_tax_rate' => $validated['default_tax_rate'] ?? '10',
            'tax_label' => $validated['tax_label'] ?? 'VAT',
            'tax_number' => $validated['tax_number'] ?? '',
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
        if (!str_starts_with($key, 'qr_') && !str_starts_with($key, 'pm_')) {
            return redirect()->back()->with('error', 'Invalid payment method key.');
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
        $payload = trim($request->input('qr_payload', ''));

        // If payload is empty and image was uploaded, try to auto-read QR payload
        if (empty($payload) && $request->hasFile('qr_image')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->attach(
                    'file', 
                    file_get_contents($request->file('qr_image')->getRealPath()), 
                    $request->file('qr_image')->getClientOriginalName()
                )->post('https://api.qrserver.com/v1/read-qr-code/');

                if ($response->successful()) {
                    $result = $response->json();
                    $qrText = $result[0]['symbol'][0]['data'] ?? null;
                    if ($qrText) {
                        $payload = $qrText;
                    }
                }
            } catch (\Throwable $e) {}
        }

        Setting::updateOrCreate(['key' => $key . '_payload'], ['value' => $payload]);

        // If key is qr_bakong, keep legacy company_bank_qr_payload in sync
        if ($key === 'qr_bakong') {
            Setting::updateOrCreate(['key' => 'company_bank_qr_payload'], ['value' => $payload]);
        }

        return redirect()->back()->with('success', 'QR Code និង KHQR Payload បានរក្សាទុកដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Delete a QR code image and its payload text for a payment method, and hide it from systems.
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

        // 1. Save to deleted_default_qr setting
        $deletedSetting = Setting::where('key', 'deleted_default_qr')->first();
        $deletedList = $deletedSetting ? json_decode($deletedSetting->value, true) : [];
        if (!is_array($deletedList)) {
            $deletedList = [];
        }
        if (!in_array($key, $deletedList)) {
            $deletedList[] = $key;
            Setting::updateOrCreate(['key' => 'deleted_default_qr'], ['value' => json_encode($deletedList)]);
        }

        // 2. Hide from checkout/sales screens via hidden_payment_methods
        $hiddenSetting = Setting::where('key', 'hidden_payment_methods')->first();
        $hiddenList = $hiddenSetting ? json_decode($hiddenSetting->value, true) : [];
        if (!is_array($hiddenList)) {
            $hiddenList = [];
        }
        $keyVariants = [$key];
        if (str_starts_with($key, 'qr_')) {
            $keyVariants[] = str_replace('qr_', '', $key) . '_qr';
        }
        foreach ($keyVariants as $variant) {
            if (!in_array($variant, $hiddenList)) {
                $hiddenList[] = $variant;
            }
        }
        Setting::updateOrCreate(['key' => 'hidden_payment_methods'], ['value' => json_encode(array_values(array_unique($hiddenList)))]);

        // 3. Reset default_payment_method if it was set to this key
        $defaultPm = Setting::where('key', 'default_payment_method')->value('value');
        if ($defaultPm === $key) {
            Setting::updateOrCreate(['key' => 'default_payment_method'], ['value' => 'pm_cash']);
        }

        if ($key === 'qr_bakong') {
            $oldBakong = Setting::where('key', 'company_bank_qr')->first();
            if ($oldBakong && $oldBakong->value && Storage::disk('public')->exists($oldBakong->value)) {
                Storage::disk('public')->delete($oldBakong->value);
            }
            Setting::where('key', 'company_bank_qr')->delete();
            Setting::where('key', 'company_bank_qr_payload')->delete();
        }

        return redirect()->back()->with('success', 'វិធីសាស្ត្រទូទាត់ត្រូវបានលុបដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Add a custom bank QR code or restore a default provider QR code.
     */
    public function addCustomQr(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'qr_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ]);

        $bankName = trim($request->bank_name);
        $path = $request->file('qr_image')->store('qr', 'public');
        $payload = trim($request->input('qr_payload', ''));

        // If payload is empty and QR image file exists, try auto-reading QR code payload
        if (empty($payload) && $request->hasFile('qr_image')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->attach(
                    'file', 
                    file_get_contents($request->file('qr_image')->getRealPath()), 
                    $request->file('qr_image')->getClientOriginalName()
                )->post('https://api.qrserver.com/v1/read-qr-code/');

                if ($response->successful()) {
                    $result = $response->json();
                    $qrText = $result[0]['symbol'][0]['data'] ?? null;
                    if ($qrText) {
                        $payload = $qrText;
                    }
                }
            } catch (\Throwable $e) {}
        }

        $defaultKeyMap = [
            'aba bank' => 'qr_aba',
            'acleda bank' => 'qr_acleda',
            'wing bank' => 'qr_wing',
            'truemoney' => 'qr_truemoney',
        ];

        $lowerName = strtolower($bankName);
        $matchedDefaultKey = $defaultKeyMap[$lowerName] ?? null;

        if ($matchedDefaultKey) {
            $key = $matchedDefaultKey;

            // Remove from deleted_default_qr
            $deletedSetting = Setting::where('key', 'deleted_default_qr')->first();
            if ($deletedSetting && $deletedSetting->value) {
                $deletedList = json_decode($deletedSetting->value, true);
                if (is_array($deletedList)) {
                    $deletedList = array_values(array_diff($deletedList, [$key]));
                    Setting::updateOrCreate(['key' => 'deleted_default_qr'], ['value' => json_encode($deletedList)]);
                }
            }

            // Remove from hidden_payment_methods
            $hiddenSetting = Setting::where('key', 'hidden_payment_methods')->first();
            if ($hiddenSetting && $hiddenSetting->value) {
                $hiddenList = json_decode($hiddenSetting->value, true);
                if (is_array($hiddenList)) {
                    $keyVariants = [$key, str_replace('qr_', '', $key) . '_qr'];
                    $hiddenList = array_values(array_diff($hiddenList, $keyVariants));
                    Setting::updateOrCreate(['key' => 'hidden_payment_methods'], ['value' => json_encode($hiddenList)]);
                }
            }
        } else {
            $key = 'qr_custom_' . time() . '_' . rand(100, 999);

            // Save metadata into custom_qr_list JSON
            $customListSetting = Setting::where('key', 'custom_qr_list')->first();
            $customList = $customListSetting ? json_decode($customListSetting->value, true) : [];
            if (!is_array($customList)) {
                $customList = [];
            }

            $customList[] = [
                'key' => $key,
                'label' => $bankName,
                'icon' => '🏦',
            ];

            Setting::updateOrCreate(['key' => 'custom_qr_list'], ['value' => json_encode($customList)]);
        }

        // Save image and payload
        Setting::updateOrCreate(['key' => $key], ['value' => $path]);
        if (!empty($payload)) {
            Setting::updateOrCreate(['key' => $key . '_payload'], ['value' => $payload]);
        }

        // Also ensure PaymentMethod exists so it shows in checkout choices
        \App\Models\PaymentMethod::firstOrCreate(['name' => $bankName]);

        return redirect()->back()->with('success', 'បានបន្ថែម QR Code ធនាគារ ' . $bankName . ' ដោយជោគជ័យ!')->with('qr_tab', true);
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
        Setting::where('key', $key . '_payload')->delete();

        // Update custom_qr_list JSON
        $customListSetting = Setting::where('key', 'custom_qr_list')->first();
        $label = null;
        if ($customListSetting && $customListSetting->value) {
            $customList = json_decode($customListSetting->value, true);
            if (is_array($customList)) {
                foreach ($customList as $item) {
                    if (isset($item['key']) && $item['key'] === $key) {
                        $label = $item['label'] ?? null;
                    }
                }
                $customList = array_values(array_filter($customList, function ($item) use ($key) {
                    return isset($item['key']) && $item['key'] !== $key;
                }));
                Setting::updateOrCreate(['key' => 'custom_qr_list'], ['value' => json_encode($customList)]);
            }
        }

        // Remove from hidden_payment_methods if present
        $hiddenSetting = Setting::where('key', 'hidden_payment_methods')->first();
        if ($hiddenSetting && $hiddenSetting->value) {
            $hiddenList = json_decode($hiddenSetting->value, true);
            if (is_array($hiddenList)) {
                $hiddenList = array_values(array_diff($hiddenList, [$key]));
                Setting::updateOrCreate(['key' => 'hidden_payment_methods'], ['value' => json_encode($hiddenList)]);
            }
        }

        // Clean up PaymentMethod if no payments exist
        if ($label) {
            $pm = \App\Models\PaymentMethod::where('name', $label)->first();
            if ($pm) {
                $hasPayments = \App\Models\Payment::where('payment_method_id', $pm->id)->exists();
                if (!$hasPayments) {
                    $pm->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'បានលុប QR Code ធនាគារដោយជោគជ័យ!')->with('qr_tab', true);
    }

    /**
     * Set a payment method as default.
     */
    public function setDefaultPaymentMethod(Request $request)
    {
        $key = $request->input('key');
        if (!empty($key)) {
            Setting::updateOrCreate(['key' => 'default_payment_method'], ['value' => $key]);
            Setting::updateOrCreate(['key' => 'last_payment_method_updated_by'], ['value' => auth()->user()->name ?? 'Admin']);
            Setting::updateOrCreate(['key' => 'last_payment_method_updated_at'], ['value' => now()->format('d M Y h:i A')]);
            return redirect(route('admin.settings.index') . '#qr')->with('success', 'បានកំណត់វិធីសាស្ត្រទូទាត់ចម្បង (Default) ដោយជោគជ័យ!');
        }
        return redirect(route('admin.settings.index') . '#qr')->with('error', 'មិនអាចកំណត់ជា Default បានទេ។');
    }

    /**
     * Toggle status of a payment method (Enabled / Disabled).
     */
    public function togglePaymentMethod(Request $request, string $key)
    {
        $hiddenSetting = Setting::where('key', 'hidden_payment_methods')->first();
        $hiddenList = $hiddenSetting ? json_decode($hiddenSetting->value, true) : [];
        if (!is_array($hiddenList)) {
            $hiddenList = [];
        }

        // Map key variants (e.g. aba_qr <-> qr_aba)
        $keyVariants = [$key];
        if (str_starts_with($key, 'qr_')) {
            $keyVariants[] = str_replace('qr_', '', $key) . '_qr';
        } elseif (str_ends_with($key, '_qr')) {
            $keyVariants[] = 'qr_' . str_replace('_qr', '', $key);
        }

        $isCurrentlyHidden = false;
        foreach ($keyVariants as $variant) {
            if (in_array($variant, $hiddenList)) {
                $isCurrentlyHidden = true;
                break;
            }
        }

        if ($isCurrentlyHidden) {
            // Re-enable by removing all variants from hiddenList
            $hiddenList = array_values(array_diff($hiddenList, $keyVariants));
            $action = 'Enabled';
            $isActive = true;
        } else {
            // Disable by adding all variants to hiddenList
            foreach ($keyVariants as $variant) {
                $hiddenList[] = $variant;
            }
            $hiddenList = array_values(array_unique($hiddenList));
            $action = 'Disabled';
            $isActive = false;
        }

        Setting::updateOrCreate(['key' => 'hidden_payment_methods'], ['value' => json_encode($hiddenList)]);

        Setting::updateOrCreate(['key' => 'last_payment_method_updated_by'], ['value' => auth()->user()->name ?? 'Admin']);
        Setting::updateOrCreate(['key' => 'last_payment_method_updated_at'], ['value' => now()->format('d M Y h:i A')]);

        return redirect(route('admin.settings.index') . '#qr')->with('success', "វិធីសាស្ត្រទូទាត់ត្រូវបាន {$action} ដោយជោគជ័យ!");
    }
}

