<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['qr_text'])) {
        file_put_contents(__DIR__ . '/qr_text.txt', $data['qr_text']);
        echo json_encode(['status' => 'success']);
        exit;
    }
}
echo json_encode(['status' => 'error']);
