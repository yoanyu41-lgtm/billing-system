<?php
/**
 * Load Khmer fonts into DomPDF
 * Run: php load-dompdf-fonts.php
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

echo "=== DomPDF Khmer Font Loader ===\n\n";

$fontDir = __DIR__ . '/storage/fonts';
$fontFile = $fontDir . '/KhmerUI.ttf';

if (!file_exists($fontFile)) {
    echo "✗ Font file not found: $fontFile\n";
    echo "Copying from Windows fonts...\n";
    
    $windowsFont = 'C:\\Windows\\Fonts\\KhmerUI.ttf';
    if (file_exists($windowsFont)) {
        copy($windowsFont, $fontFile);
        echo "✓ Copied KhmerUI.ttf\n";
    } else {
        echo "✗ Windows font not found\n";
        exit(1);
    }
}

echo "Font file: $fontFile\n";
echo "File size: " . number_format(filesize($fontFile)) . " bytes\n\n";

// Initialize DomPDF with font options
$options = new Options();
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->set('isPhpEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Load font metrics
echo "Loading font into DomPDF...\n";

$fontMetrics = $dompdf->getFontMetrics();

try {
    // Register the font
    $fontMetrics->registerFont([
        'family' => 'khmeros',
        'style' => 'normal',
        'weight' => 'normal',
    ], $fontFile);
    
    echo "✓ Font 'khmeros' registered successfully!\n\n";
    echo "You can now use font-family: 'khmeros' in your PDF templates.\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
