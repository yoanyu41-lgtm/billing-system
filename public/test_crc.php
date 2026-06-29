<?php

function calculateCRC16_JS($payload) {
    $crc = 0xFFFF;
    for ($c = 0; $c < strlen($payload); $c++) {
        $crc ^= ord($payload[$c]) << 8;
        for ($i = 0; $i < 8; $i++) {
            if ($crc & 0x8000) {
                $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
            } else {
                $crc = ($crc << 1) & 0xFFFF;
            }
        }
    }
    return str_pad(strtoupper(dechex($crc)), 4, '0', STR_PAD_LEFT);
}

function calculateCRC16_PHP($payload) {
    $crc = 0xFFFF;
    for ($c = 0; $c < strlen($payload); $c++) {
        $crc ^= ord($payload[$c]) << 8;
        for ($i = 0; $i < 8; $i++) {
            if ($crc & 0x8000) {
                $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
            } else {
                $crc = ($crc << 1) & 0xFFFF;
            }
        }
    }
    return sprintf('%04X', $crc);
}

$payload = "00020101021129350013kh.gov.bakong0114069244286@wing520459995303840540510.005802KH5907Yu YOAN6009Siem Reap6304";
echo "JS CRC: " . calculateCRC16_JS($payload) . "\n";
echo "PHP CRC: " . calculateCRC16_PHP($payload) . "\n";
