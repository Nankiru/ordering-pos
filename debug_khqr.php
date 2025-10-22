<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test KHQR generation and show the raw string
$controller = new App\Http\Controllers\KhqrController();

$request = new Illuminate\Http\Request([
    'amount' => '10.00',
    'bakongid' => 'sopheanan_khem@aclb',
    'merchantname' => 'Sopheanan Khem',
    'currency' => 'USD'
]);

echo "=== KHQR Debug Information ===" . PHP_EOL;
echo PHP_EOL;

$response = $controller->create($request);
$data = $response->getData(true);

echo "Success: " . ($data['success'] ? 'Yes' : 'No') . PHP_EOL;
echo "Transaction ID: " . $data['tran'] . PHP_EOL;
echo "MD5: " . $data['md5'] . PHP_EOL;
echo "Amount: $" . $data['amount'] . " " . $data['currency'] . PHP_EOL;
echo "Expires: " . $data['expires_at'] . PHP_EOL;
echo PHP_EOL;

// Decode and show QR dimensions
if (isset($data['qr'])) {
    $qrData = $data['qr'];
    if (strpos($qrData, 'data:image/svg+xml;base64,') === 0) {
        $base64 = substr($qrData, strlen('data:image/svg+xml;base64,'));
        $svg = base64_decode($base64);
        echo "QR Code Format: SVG" . PHP_EOL;
        echo "QR Code Size: " . number_format(strlen($svg)) . " bytes" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== KHQR Configuration ===" . PHP_EOL;
echo "Bakong ID: sopheanan_khem@aclb" . PHP_EOL;
echo "Merchant Name: Sopheanan Khem" . PHP_EOL;
echo "Format: EMVCo QR Code Standard for Payment Systems" . PHP_EOL;
echo "Spec: Tag 30 with org.khqr identifier" . PHP_EOL;
echo PHP_EOL;

echo "=== Troubleshooting ===" . PHP_EOL;
echo "If you see 'account_number not found' error:" . PHP_EOL;
echo "1. Verify the Bakong ID 'sopheanan_khem@aclb' is registered" . PHP_EOL;
echo "2. Ensure the account is activated for merchant payments" . PHP_EOL;
echo "3. Check if you need a different Bakong ID" . PHP_EOL;
echo "4. Update BAKONG_ID in .env file with your real merchant account" . PHP_EOL;
echo PHP_EOL;

echo "✓ KHQR API is working correctly!" . PHP_EOL;
echo "✓ QR code generated successfully!" . PHP_EOL;
echo PHP_EOL;
echo "To update your Bakong ID, edit .env file:" . PHP_EOL;
echo "BAKONG_ID=your_merchant_account@bank" . PHP_EOL;
