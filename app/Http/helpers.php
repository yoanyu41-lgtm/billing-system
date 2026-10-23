<?php

if (!function_exists('format_currency')) {
    function format_currency($usdAmount, $exchangeRate = null) {
        return '$' . number_format((float) $usdAmount, 2);
    }
}
