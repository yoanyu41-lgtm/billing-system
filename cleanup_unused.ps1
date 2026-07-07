$base = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host "=== Billing System Cleanup ===" -ForegroundColor Cyan
Write-Host "Root: $base" -ForegroundColor Gray

# ============================================================
# 1. FILES IN public/ (debug/scratch/temp files)
# ============================================================
$publicFiles = @(
    'all_small_php.txt',
    'analyze_transcript.php',
    'check_git.php',
    'cleanup.php',
    'debug_create.php',
    'debug_qr_state.php',
    'debug_restore.php',
    'decode.html',
    'decode.php',
    'decode_db_qr.php',
    'decode_latest.php',
    'decode_new_uploaded.php',
    'decode_saved_image.php',
    'decode_temp_media.php',
    'decode_uploaded.php',
    'decode_via_api.php',
    'decode_zxing.php',
    'dump_small_php.php',
    'extract_transcript.php',
    'extract_transcript_output.txt',
    'find_deleted.php',
    'find_deleted_output.txt',
    'find_khqr_blobs.php',
    'generate_merchant_payload.php',
    'get_active_generated.php',
    'git_info.php',
    'git_push_dynamic_details.php',
    'git_show_last_commit.php',
    'qr_decoder.html',
    'reconstruct.php',
    'restore.php',
    'restore_final.php',
    'restore_p2p.php',
    'save_qr_text.php',
    'scan_images.php',
    'search_blobs.php',
    'search_transcript_all_output.txt',
    'search_transcript_serve.php',
    'test_chrome.php',
    'test_reconstruction.php',
    'test_tree.php'
)

Write-Host "`n[1] Cleaning public/ debug files..." -ForegroundColor Yellow
foreach ($f in $publicFiles) {
    $path = Join-Path $base "public\$f"
    if (Test-Path $path) {
        Remove-Item -Force $path
        Write-Host "  DELETED: public\$f" -ForegroundColor Green
    }
}

# ============================================================
# 2. FILES IN root/ (docs/scratch/temp files)
# ============================================================
$rootFiles = @(
    'API-V1-CORE.txt',
    'BRAND-COLORS.md',
    'COMPANY-SETTINGS-KM.md',
    'COMPANY-SETTINGS.md',
    'DATABASE-COMPLETE-SCHEMA.txt',
    'DATABASE-SUMMARY.txt',
    'DESIGN-SYSTEM.md',
    'FONT-KHMER-OS-SIEMREAP.md',
    'FONT-SETUP.md',
    'IMPLEMENTATION-SUMMARY.md',
    'KHMER-FONT-FIX.md',
    'SYSTEM-ROUTES-MENU.txt',
    'TAX-IMPLEMENTATION-TODO.md',
    'TAX-PROGRESS.md',
    '_seed_terms.php',
    'decode_branch.php',
    'find_missing_lang.php',
    'install-khmer-font.php',
    'load-dompdf-fonts.php',
    'load-khmer-siemreap.php',
    'test_khqr_temp.php',
    'tmp_image_check.php',
    'postman_collection.json',
    'README-KM.md'
)

Write-Host "`n[2] Cleaning root/ scratch files..." -ForegroundColor Yellow
foreach ($f in $rootFiles) {
    $path = Join-Path $base $f
    if (Test-Path $path) {
        Remove-Item -Force $path
        Write-Host "  DELETED: $f" -ForegroundColor Green
    }
}

# ============================================================
# 3. EMPTY storage temp folders
# ============================================================
Write-Host "`n[3] Cleaning storage temp folders..." -ForegroundColor Yellow
$tempFolders = @(
    'storage\app\public\temp_pdf',
    'storage\app\public\temp_qrs',
    'storage\app\public\contracts_temp'
)
foreach ($folder in $tempFolders) {
    $path = Join-Path $base $folder
    if (Test-Path $path) {
        Get-ChildItem -Path $path -File | Remove-Item -Force
        Write-Host "  CLEARED: $folder (kept folder)" -ForegroundColor Green
    }
}

# ============================================================
# 4. Clear laravel.log (keep file, clear content)
# ============================================================
Write-Host "`n[4] Clearing laravel.log..." -ForegroundColor Yellow
$logPath = Join-Path $base "storage\logs\laravel.log"
if (Test-Path $logPath) {
    Clear-Content $logPath
    Write-Host "  CLEARED: storage\logs\laravel.log" -ForegroundColor Green
}

Write-Host "`n=== CLEANUP COMPLETE ===" -ForegroundColor Cyan
Write-Host "You can now delete this script: cleanup_unused.ps1" -ForegroundColor Gray
