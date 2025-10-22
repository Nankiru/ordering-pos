<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KhqrController extends Controller
{
    /**
     * Generate KHQR code
     * POST /api/khqr/create
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bakongid' => 'required|string',
            'merchantname' => 'required|string',
        ]);

        $amount = (float) $request->input('amount');
        $bakongid = $request->input('bakongid');
        $merchantname = $request->input('merchantname');
        $currency = $request->input('currency', 'USD'); // USD or KHR

        // Validate Bakong ID format (should be account@bank)
        if (strpos($bakongid, '@') === false) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Bakong ID format. Expected format: account@bank (e.g., username@wing)',
            ], 400);
        }

        // Generate unique transaction reference
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(8));
        $tran = "TXN{$timestamp}{$random}";

        // Generate MD5 hash for transaction tracking
        $md5 = md5($tran . $bakongid . $amount . $timestamp);

        // Create KHQR data string (simplified format)
        // In production, this should follow the EMVCo QR Code Specification
        $khqrData = $this->generateKHQRString([
            'bakongid' => $bakongid,
            'merchantname' => $merchantname,
            'amount' => $amount,
            'currency' => $currency,
            'tran' => $tran,
        ]);

        // Generate QR code image as base64 using SVG (no imagick required)
        $qrSvg = QrCode::format('svg')
            ->size(400)
            ->errorCorrection('H')
            ->generate($khqrData);

        // Convert SVG to base64 data URI
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        // Store transaction in session or database for verification
        $transactionData = [
            'md5' => $md5,
            'tran' => $tran,
            'bakongid' => $bakongid,
            'amount' => $amount,
            'currency' => $currency,
            'merchantname' => $merchantname,
            'status' => 'pending', // pending, paid, expired
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ];

        // Store in cache for 15 minutes
        cache()->put("khqr_txn_{$md5}", $transactionData, now()->addMinutes(15));

        return response()->json([
            'success' => true,
            'qr' => $qrBase64,
            'md5' => $md5,
            'tran' => $tran,
            'amount' => $amount,
            'currency' => $currency,
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ]);
    }

    /**
     * Check payment status by MD5
     * GET /api/khqr/check_by_md5?md5={md5}&bakongid={bakongid}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkByMd5(Request $request)
    {
        $md5 = $request->query('md5');
        $bakongid = $request->query('bakongid');

        if (!$md5 || !$bakongid) {
            return response()->json([
                'success' => false,
                'responseCode' => 1,
                'message' => 'Missing required parameters',
            ], 400);
        }

        // Retrieve transaction from cache
        $transaction = cache()->get("khqr_txn_{$md5}");

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'responseCode' => 2,
                'message' => 'Transaction not found or expired',
            ], 404);
        }

        // Verify bakongid matches
        if ($transaction['bakongid'] !== $bakongid) {
            return response()->json([
                'success' => false,
                'responseCode' => 3,
                'message' => 'Invalid credentials',
            ], 403);
        }

        // Check if transaction is expired
        if (now()->timestamp > $transaction['expires_at']) {
            return response()->json([
                'success' => false,
                'responseCode' => 4,
                'message' => 'Transaction expired',
            ]);
        }

        // Return payment status
        // responseCode: 0 = paid, 1 = pending
        $responseCode = $transaction['status'] === 'paid' ? 0 : 1;

        return response()->json([
            'success' => true,
            'responseCode' => $responseCode,
            'status' => $transaction['status'],
            'amount' => $transaction['amount'],
            'tran' => $transaction['tran'],
        ]);
    }

    /**
     * Simulate payment (for testing purposes)
     * POST /api/khqr/simulate-payment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function simulatePayment(Request $request)
    {
        $md5 = $request->input('md5');

        if (!$md5) {
            return response()->json([
                'success' => false,
                'message' => 'Missing MD5 parameter',
            ], 400);
        }

        // Retrieve transaction from cache
        $transaction = cache()->get("khqr_txn_{$md5}");

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // Mark as paid
        $transaction['status'] = 'paid';
        $transaction['paid_at'] = now()->timestamp;

        // Update cache
        cache()->put("khqr_txn_{$md5}", $transaction, now()->addMinutes(15));

        return response()->json([
            'success' => true,
            'message' => 'Payment simulated successfully',
            'md5' => $md5,
            'status' => 'paid',
        ]);
    }

    /**
     * Generate KHQR string according to EMVCo specification
     *
     * @param array $data
     * @return string
     */
    private function generateKHQRString(array $data): string
    {
        // Build KHQR according to Bakong specification
        $qrString = '';

        // 00: Payload Format Indicator
        $qrString .= $this->formatTLV('00', '01');

        // 01: Point of Initiation Method (11 = static, 12 = dynamic)
        $qrString .= $this->formatTLV('01', '11');

        // 30: Merchant Account Information (KHQR/Bakong specification)
        // According to Bakong KHQR spec, tag 30 must contain:
        // - Tag 00: Globally Unique Identifier (org.khqr)
        // - Tag 01: Acquiring Bank / Payment network
        // - Tag 02: Merchant Account Number / Bakong ID
        $merchantAccountInfo = '';
        $merchantAccountInfo .= $this->formatTLV('00', 'org.khqr'); // Globally Unique Identifier

        // Extract bank code and account from bakongid (format: account@bank)
        $bakongParts = explode('@', $data['bakongid']);
        if (count($bakongParts) === 2) {
            $accountNumber = $bakongParts[0]; // sopheanan_khem
            $bankCode = $bakongParts[1];      // aclb

            // Tag 01: Acquiring Bank
            $merchantAccountInfo .= $this->formatTLV('01', $bankCode);

            // Tag 02: Merchant Account Number
            $merchantAccountInfo .= $this->formatTLV('02', $accountNumber);
        } else {
            // Fallback if format is not account@bank
            $merchantAccountInfo .= $this->formatTLV('01', $data['bakongid']);
        }

        $qrString .= $this->formatTLV('30', $merchantAccountInfo);

        // 52: Merchant Category Code
        $qrString .= $this->formatTLV('52', '5999'); // Miscellaneous retail stores

        // 53: Transaction Currency (116=KHR, 840=USD)
        $currencyCode = $data['currency'] === 'KHR' ? '116' : '840';
        $qrString .= $this->formatTLV('53', $currencyCode);

        // 54: Transaction Amount
        $qrString .= $this->formatTLV('54', number_format($data['amount'], 2, '.', ''));

        // 58: Country Code
        $qrString .= $this->formatTLV('58', 'KH');

        // 59: Merchant Name
        $qrString .= $this->formatTLV('59', substr($data['merchantname'], 0, 25));

        // 60: Merchant City
        $qrString .= $this->formatTLV('60', 'Phnom Penh');

        // 62: Additional Data Field
        $additionalData = '';
        $additionalData .= $this->formatTLV('05', $data['tran']); // Reference Label
        $qrString .= $this->formatTLV('62', $additionalData);

        // 63: CRC (must be last) - calculate CRC16-CCITT
        $qrString .= '6304'; // CRC tag and length
        $crc = $this->calculateCRC16($qrString);
        $qrString .= $crc;

        return $qrString;
    }

    /**
     * Format TLV (Tag-Length-Value) structure
     *
     * @param string $tag
     * @param string $value
     * @return string
     */
    private function formatTLV(string $tag, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $length . $value;
    }

    /**
     * Calculate CRC16-CCITT checksum
     *
     * @param string $data
     * @return string
     */
    private function calculateCRC16(string $data): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        for ($i = 0; $i < strlen($data); $i++) {
            $byte = ord($data[$i]);
            $crc ^= ($byte << 8);

            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ $polynomial;
                } else {
                    $crc = $crc << 1;
                }
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(sprintf('%04X', $crc));
    }
}
