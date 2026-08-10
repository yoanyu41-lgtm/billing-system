<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'details',
    ];

    /**
     * Get active payment methods filtered by user settings (hidden or deleted ones excluded).
     */
    public static function getAvailable()
    {
        $hiddenSetting = Setting::where('key', 'hidden_payment_methods')->value('value');
        $hiddenList = $hiddenSetting ? json_decode($hiddenSetting, true) : [];
        if (!is_array($hiddenList)) {
            $hiddenList = [];
        }

        $deletedSetting = Setting::where('key', 'deleted_default_qr')->value('value');
        $deletedList = $deletedSetting ? json_decode($deletedSetting, true) : [];
        if (!is_array($deletedList)) {
            $deletedList = [];
        }

        $allHidden = array_unique(array_merge($hiddenList, $deletedList));

        // Get custom QR code labels that are hidden
        $customListSetting = Setting::where('key', 'custom_qr_list')->value('value');
        $customList = $customListSetting ? json_decode($customListSetting, true) : [];
        $hiddenLabels = [];
        if (is_array($customList)) {
            foreach ($customList as $cItem) {
                if (!empty($cItem['key']) && in_array($cItem['key'], $allHidden)) {
                    if (!empty($cItem['label'])) {
                        $hiddenLabels[] = strtolower(trim($cItem['label']));
                    }
                }
            }
        }

        return static::whereNotIn('name', ['QR Code', 'Bank Transfer', 'QR'])
            ->orderBy('name')
            ->get()
            ->reject(function ($method) use ($allHidden, $hiddenLabels) {
                $name = strtolower(trim($method->name));

                if (in_array($name, $hiddenLabels)) {
                    return true;
                }

                // Cash / សាច់ប្រាក់
                if ($name === 'cash' || str_contains($name, 'សាច់ប្រាក់')) {
                    if (in_array('pm_cash', $allHidden) || in_array('cash', $allHidden)) return true;
                }

                // Credit Card / កាត់ឥណទាន
                if ($name === 'credit card' || str_contains($name, 'កាតឥណទាន') || str_contains($name, 'card')) {
                    if (in_array('pm_card', $allHidden) || in_array('qr_creditcard', $allHidden) || in_array('creditcard_qr', $allHidden) || in_array('credit_card', $allHidden)) return true;
                }

                // ABA Bank / ធនាគារ ABA
                if (str_contains($name, 'aba')) {
                    if (in_array('aba_qr', $allHidden) || in_array('qr_aba', $allHidden) || in_array('aba', $allHidden)) return true;
                }

                // ACLEDA Bank / ធនាគារ អេស៊ីលីដា
                if (str_contains($name, 'acleda') || str_contains($name, 'អេស៊ីលីដា')) {
                    if (in_array('acleda_qr', $allHidden) || in_array('qr_acleda', $allHidden) || in_array('acleda', $allHidden)) return true;
                }

                // Wing Bank / ធនាគារ វីង
                if (str_contains($name, 'wing') || str_contains($name, 'វីង')) {
                    if (in_array('wing_qr', $allHidden) || in_array('qr_wing', $allHidden) || in_array('wing', $allHidden)) return true;
                }

                // TrueMoney / ទ្រូម៉ានី
                if (str_contains($name, 'truemoney') || str_contains($name, 'ទ្រូម៉ានី')) {
                    if (in_array('truemoney_qr', $allHidden) || in_array('qr_truemoney', $allHidden) || in_array('truemoney', $allHidden)) return true;
                }

                // Bakong / បាគង
                if (str_contains($name, 'bakong') || str_contains($name, 'បាគង')) {
                    if (in_array('bakong_qr', $allHidden) || in_array('qr_bakong', $allHidden) || in_array('bakong', $allHidden)) return true;
                }

                return false;
            })
            ->values();
    }
}

