<?php

if (!function_exists('format_currency')) {
    function format_currency($usdAmount, $exchangeRate = null) {
        $currency = session('display_currency', 'USD');
        if ($currency === 'KHR') {
            if ($exchangeRate === null) {
                $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
            }
            return number_format(round($usdAmount * $exchangeRate)) . ' ៛';
        }
        return '$' . number_format($usdAmount, 2);
    }
}
