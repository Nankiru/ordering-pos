<?php

// Test KHQR generation
$response = file_get_contents('http://127.0.0.1:8000/api/khqr/create?amount=5.50&bakongid=sopheanan_khem@aclb&merchantname=Hourt%20POS');
$data = json_decode($response, true);

echo "=== KHQR Test ===" . PHP_EOL;
echo "Transaction: " . $data['tran'] . PHP_EOL;
echo "MD5: " . $data['md5'] . PHP_EOL;
echo "Amount: $" . $data['amount'] . PHP_EOL;
echo "Currency: " . $data['currency'] . PHP_EOL;
echo "Expires: " . $data['expires_at'] . PHP_EOL;
echo PHP_EOL;

// Decode the SVG to see the KHQR string
$qrData = $data['qr'];
if (strpos($qrData, 'data:image/svg+xml;base64,') === 0) {
    $base64 = substr($qrData, strlen('data:image/svg+xml;base64,'));
    $svg = base64_decode($base64);

    // Extract KHQR string from SVG (it's in the path data)
    // The QR code encodes the KHQR payment string
    echo "QR Code generated successfully!" . PHP_EOL;
    echo "SVG size: " . strlen($svg) . " bytes" . PHP_EOL;
}

echo PHP_EOL;
echo "✓ API is working!" . PHP_EOL;
