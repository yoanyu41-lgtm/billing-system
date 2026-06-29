<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== AUTOMATIC RECOVERY SCRIPT ===\n";

// 1. Run git fsck to get all dangling objects
$fsckOutput = shell_exec('git fsck --lost-found 2>&1');
echo "Running git fsck...\n";

// Find all tree and commit hashes from the fsck output
preg_match_all('/dangling tree ([a-f0-9]{40})/', $fsckOutput, $treeMatches);
preg_match_all('/dangling commit ([a-f0-9]{40})/', $fsckOutput, $commitMatches);
preg_match_all('/dangling blob ([a-f0-9]{40})/', $fsckOutput, $blobMatches);

$trees = array_unique(array_merge($treeMatches[1], $commitMatches[1]));
$allBlobs = array_unique($blobMatches[1]);

echo "Found " . count($trees) . " dangling trees/commits.\n";
echo "Found " . count($allBlobs) . " dangling blobs.\n\n";

$restoredFiles = [];
$matchedBlobs = [];

// 2. Parse trees to get file paths
foreach ($trees as $treeHash) {
    echo "Scanning tree/commit: $treeHash...\n";
    $treeContent = shell_exec("git ls-tree -r $treeHash 2>&1");
    if (empty($treeContent)) continue;

    $lines = explode("\n", trim($treeContent));
    foreach ($lines as $line) {
        // Line format: 100644 blob <hash>    <path>
        if (preg_match('/^\d+ blob ([a-f0-9]{40})\t(.+)$/', $line, $matches)) {
            $blobHash = $matches[1];
            $filePath = $matches[2];
            
            $lostFile = ".git/lost-found/other/$blobHash";
            if (file_exists($lostFile)) {
                $dir = dirname($filePath);
                if (!file_exists($dir) && !empty($dir)) {
                    mkdir($dir, 0755, true);
                }
                copy($lostFile, $filePath);
                $restoredFiles[$filePath] = $blobHash;
                $matchedBlobs[$blobHash] = $filePath;
                echo " [RESTORED VIA TREE] $filePath\n";
            }
        }
    }
}

// 3. Fallback: Content-based matching for remaining blobs
echo "\nChecking unmatched blobs...\n";
foreach ($allBlobs as $blobHash) {
    if (isset($matchedBlobs[$blobHash])) continue;

    $lostFile = ".git/lost-found/other/$blobHash";
    if (!file_exists($lostFile)) continue;

    $content = file_get_contents($lostFile);
    $filePath = null;

    if (strpos($content, 'class ServeCommand') !== false) {
        $filePath = 'app/Console/Commands/ServeCommand.php';
    } elseif (strpos($content, 'កិច្ចសន្យាបង់រំលស់') !== false && strpos($content, 'paymentSchedule') !== false) {
        $filePath = 'resources/views/installments/contract_print.blade.php';
    } elseif (strpos($content, 'save_qr_text') !== false || strpos($content, 'qr_text.txt') !== false) {
        $filePath = 'public/save_qr_text.php';
    } elseif (strpos($content, 'test_generator_final') !== false) {
        $filePath = 'public/test_generator_final.php';
    } elseif (strpos($content, 'test_settings_js') !== false) {
        $filePath = 'public/test_settings_js.php';
    } elseif (strpos($content, 'khmer_ui_bold') !== false) {
        $filePath = 'storage/fonts/khmer_ui_bold_dbaa61fd1f4ca6cdc0bec2dd94eb19ca.ufm.json';
    } elseif (strpos($content, 'khmer_ui_normal') !== false) {
        $filePath = 'storage/fonts/khmer_ui_normal_56da52e3e71a4dd22d6ac63756dcdc1f.ufm.json';
    } elseif (strpos($content, 'KHQR') !== false && strpos($content, 'test') !== false) {
        $filePath = 'test_khqr.php';
    } elseif (strpos($content, 'decode_branch') !== false) {
        $filePath = 'decode_branch.php';
    } elseif (strpos($content, 'chrome_test.html') !== false) {
        $filePath = 'public/chrome_test.html';
    }

    if ($filePath) {
        $dir = dirname($filePath);
        if (!file_exists($dir) && !empty($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($filePath, $content);
        $restoredFiles[$filePath] = $blobHash;
        echo " [RESTORED VIA CONTENT] $filePath\n";
    }
}

echo "\n=== RECOVERY COMPLETE ===\n";
echo "Total files restored: " . count($restoredFiles) . "\n";
