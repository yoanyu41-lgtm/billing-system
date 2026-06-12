<?php
/**
 * Register Khmer OS Siemreap font in DomPDF
 * Run: php load-khmer-siemreap.php
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

echo "=== Loading Khmer OS Siemreap into DomPDF ===\n\n";

$fontDir = __DIR__ . '/storage/fonts';
$fontFile = $fontDir . '/KhmerOSSiemreap.ttf';

if (!file_exists($fontFile)) {
    echo "✗ Font file not found: $fontFile\n";
    echo "Run: php install-khmer-font.php first\n";
    exit(1);
}

echo "Font: $fontFile\n";
echo "Size: " . number_format(filesize($fontFile)) . " bytes\n\n";

// Initialize DomPDF
$options = new Options();
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->set('isPhpEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

echo "Registering 'Khmer OS Siemreap' font...\n";

try {
    $fontMetrics = $dompdf->getFontMetrics();
    
    // Register normal weight
    $fontMetrics->registerFont([
        'family' => 'Khmer OS Siemreap',
        'style' => 'normal',
        'weight' => 'normal',
    ], $fontFile);
    
    echo "✓ Font registered as 'Khmer OS Siemreap'\n\n";
    echo "✓✓✓ Success! ✓✓✓\n";
    echo "\nYou can now use: font-family: 'Khmer OS Siemreap'\n";
    echo "The PDF will display Khmer text correctly.\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
