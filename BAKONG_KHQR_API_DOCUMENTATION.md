# Bakong KHQR API Documentation

## Overview
This document explains what information is needed to generate Bakong KHQR payment codes.

---

## Required Information for KHQR Payment

### 1. Merchant Information (Your Business)

#### **bakongid** (REQUIRED)
- **Type**: String
- **Format**: `account@bank` or `+855XXXXXXXXX`
- **Description**: Your registered Bakong merchant account ID
- **Examples**:
  - `yourstore@aba` (ABA Bank)
  - `shop123@wing` (Wing Bank)
  - `merchant@aclb` (ACLB Bank)
  - `business@emoney` (eMoney)
  - `+85512345678` (Phone number format)

**Important**: This must be a REAL registered merchant account in Bakong system!

#### **merchantname** (REQUIRED)
- **Type**: String
- **Max Length**: 25 characters
- **Description**: Your business/store name that customers see
- **Examples**:
  - `"Coffee Shop"` 
  - `"Nan Store"`
  - `"Electronic Shop"`

---

### 2. Transaction Information

#### **amount** (REQUIRED)
- **Type**: Numeric (decimal)
- **Min Value**: 0.01
- **Description**: Payment amount
- **Examples**:
  - `10.50`
  - `100.00`
  - `5.25`

#### **currency** (OPTIONAL)
- **Type**: String
- **Default**: `"USD"`
- **Options**: 
  - `"USD"` - US Dollar
  - `"KHR"` - Cambodian Riel
- **Description**: Payment currency

---

## API Endpoints

### 1. Create KHQR Payment

**Endpoint**: `GET /api/khqr/create`

**Method**: GET

**Parameters**:
```
amount         (required) - Payment amount (e.g., 10.00)
bakongid       (required) - Your Bakong merchant account (e.g., store@aba)
merchantname   (required) - Your business name (e.g., My Store)
currency       (optional) - USD or KHR (default: USD)
```

**Example Request**:
```
GET http://127.0.0.1:8000/api/khqr/create?amount=10.00&bakongid=yourstore@aba&merchantname=My%20Store&currency=USD
```

**Example Response**:
```json
{
    "success": true,
    "qr": "data:image/svg+xml;base64,PD94bWw...",
    "md5": "abc123def456...",
    "tran": "TXN20251018123456ABC12345",
    "amount": 10.00,
    "currency": "USD",
    "expires_at": "2025-10-18T12:44:56+07:00"
}
```

---

### 2. Check Payment Status

**Endpoint**: `GET /api/khqr/check_by_md5`

**Method**: GET

**Parameters**:
```
md5            (required) - Transaction MD5 hash from create response
bakongid       (required) - Your Bakong merchant account
```

**Example Request**:
```
GET http://127.0.0.1:8000/api/khqr/check_by_md5?md5=abc123def456&bakongid=yourstore@aba
```

**Example Response** (Pending):
```json
{
    "success": true,
    "responseCode": 1,
    "status": "pending",
    "amount": 10.00,
    "tran": "TXN20251018123456ABC12345"
}
```

**Example Response** (Paid):
```json
{
    "success": true,
    "responseCode": 0,
    "status": "paid",
    "amount": 10.00,
    "tran": "TXN20251018123456ABC12345"
}
```

**Response Codes**:
- `0` = Payment successful (PAID)
- `1` = Payment pending (NOT PAID YET)
- `2` = Transaction not found or expired
- `3` = Invalid credentials
- `4` = Transaction expired

---

### 3. Simulate Payment (Testing Only)

**Endpoint**: `POST /api/khqr/simulate-payment`

**Method**: POST

**Parameters**:
```
md5            (required) - Transaction MD5 hash
```

**Example Request**:
```bash
curl -X POST http://127.0.0.1:8000/api/khqr/simulate-payment \
  -H "Content-Type: application/json" \
  -d '{"md5":"abc123def456"}'
```

**Example Response**:
```json
{
    "success": true,
    "message": "Payment simulated successfully",
    "md5": "abc123def456",
    "status": "paid"
}
```

---

## Complete KHQR Data Structure

When you call the API, the system generates a KHQR code containing:

### Tag Structure (EMVCo Format)

```
Tag 00: Payload Format Indicator = "01"
Tag 01: Point of Initiation Method = "11" (static) or "12" (dynamic)

Tag 30: Merchant Account Information (nested)
    ├─ Tag 00: Globally Unique Identifier = "org.khqr"
    ├─ Tag 01: Bank Code (extracted from bakongid)
    │          Example: "aba" from "store@aba"
    └─ Tag 02: Account Number (extracted from bakongid)
               Example: "store" from "store@aba"

Tag 52: Merchant Category Code = "5999" (retail)
Tag 53: Transaction Currency = "840" (USD) or "116" (KHR)
Tag 54: Transaction Amount = Your amount (e.g., "10.00")
Tag 58: Country Code = "KH" (Cambodia)
Tag 59: Merchant Name = Your merchantname (max 25 chars)
Tag 60: Merchant City = "Phnom Penh"

Tag 62: Additional Data (nested)
    └─ Tag 05: Reference Label = Transaction ID

Tag 63: CRC = Calculated checksum (CRC16-CCITT)
```

---

## Configuration (In Your Laravel Project)

### Environment Variables (.env file)

```env
# Bakong/KHQR Payment Configuration
BAKONG_ID="your_merchant_account@aba"
BAKONG_MERCHANT_NAME="Your Store Name"
```

### How to Use in Code

**Example in PHP/Laravel:**
```php
$bakongid = env('BAKONG_ID', 'default@aba');
$merchantname = env('BAKONG_MERCHANT_NAME', 'My Store');

// Create KHQR payment
$khqrRequest = new Request([
    'amount' => 10.00,
    'bakongid' => $bakongid,
    'merchantname' => $merchantname,
    'currency' => 'USD'
]);

$khqrController = new KhqrController();
$response = $khqrController->create($khqrRequest);
$data = $response->getData(true);

// Use the QR code
$qrCodeImage = $data['qr'];  // Base64 encoded SVG
$md5Hash = $data['md5'];     // For checking payment status
$transactionId = $data['tran']; // Unique transaction reference
```

---

## Real-World Example Flow

### Step 1: Customer Checkout
```
Customer adds items to cart: $25.50
Customer proceeds to checkout
```

### Step 2: Generate KHQR
```php
// Your code calls:
GET /api/khqr/create?amount=25.50&bakongid=mystore@aba&merchantname=My%20Store

// Returns:
{
    "qr": "data:image/svg+xml;base64,...",  // Display this QR to customer
    "md5": "abc123",
    "tran": "TXN20251018120000XYZ"
}
```

### Step 3: Customer Scans QR
```
Customer opens Bakong/ABA app
Customer scans the QR code
Bakong app shows:
  - Merchant: My Store
  - Amount: $25.50
  - Account: mystore@aba
Customer confirms payment
```

### Step 4: Check Payment Status
```php
// Your code polls every 5 seconds:
GET /api/khqr/check_by_md5?md5=abc123&bakongid=mystore@aba

// Initially returns:
{"responseCode": 1, "status": "pending"}

// After customer pays:
{"responseCode": 0, "status": "paid"}
```

### Step 5: Complete Order
```
Payment confirmed!
Create order in database
Send confirmation to customer
```

---

## What You Need to Provide

### As a Merchant, you need:

1. ✅ **Bakong Merchant Account ID**
   - Format: `account@bank` or `+855XXXXXXXXX`
   - Get from: Bakong merchant registration
   - Example: `yourstore@aba`

2. ✅ **Business Name**
   - Your store/business name
   - Max 25 characters
   - Example: `"Electronic Shop"`

3. ✅ **Transaction Amount**
   - How much customer needs to pay
   - Decimal number (e.g., 10.50)

4. ⚠️ **Currency** (Optional)
   - Default: USD
   - Options: USD or KHR

---

## Common Bakong Account Formats

### Bank Account Format
```
username@bank
```

Examples:
- `shop123@aba` - ABA Bank
- `store456@wing` - Wing Bank
- `merchant789@aclb` - ACLB Bank
- `business@emoney` - eMoney
- `mystore@canadia` - Canadia Bank

### Phone Number Format
```
+855XXXXXXXXX
```

Example:
- `+85512345678`
- `+85598765432`

---

## Testing Your Implementation

### Test with Simulator (No Real Account Needed)
```
1. Generate QR code with any bakongid (e.g., test@aba)
2. Get the MD5 hash
3. Go to: http://127.0.0.1:8000/khqr-simulator
4. Enter MD5 hash
5. Click "Simulate Payment"
6. ✅ Payment marked as complete!
```

### Test with Real Bakong Account
```
1. Get real merchant account from Bakong
2. Update .env with real credentials
3. Generate QR code
4. Scan with Bakong/ABA mobile app
5. Complete payment
6. ✅ Real payment received!
```

---

## Security Notes

### Transaction Expiry
- QR codes expire after **10 minutes**
- Transaction data stored in cache for **15 minutes**
- After expiry, customer must generate new QR

### Verification
- Always verify `bakongid` matches your account
- Check `responseCode` before completing order
- Validate transaction amount matches order total

---

## Error Handling

### Common Errors

**"Invalid Bakong ID format"**
- Cause: bakongid doesn't contain `@` symbol
- Fix: Use format `account@bank`

**"This account number is not visible"**
- Cause: Bakong account doesn't exist in system
- Fix: Use real registered merchant account

**"Transaction not found or expired"**
- Cause: Transaction expired (>10 minutes)
- Fix: Generate new QR code

**"Invalid credentials"**
- Cause: bakongid doesn't match original transaction
- Fix: Use same bakongid for create and check

---

## Summary - What You Need

| Field | Required | Format | Example |
|-------|----------|--------|---------|
| **bakongid** | ✅ YES | `account@bank` | `mystore@aba` |
| **merchantname** | ✅ YES | String (max 25) | `"My Store"` |
| **amount** | ✅ YES | Decimal | `10.50` |
| **currency** | ❌ NO | USD or KHR | `"USD"` |

**That's it!** Just these fields to create a Bakong KHQR payment!

---

## Quick Start Code Example

```php
// In your Laravel controller

use App\Http\Controllers\KhqrController;
use Illuminate\Http\Request;

// Create KHQR payment
$khqrController = new KhqrController();

$request = new Request([
    'amount' => 25.50,                      // How much to charge
    'bakongid' => 'mystore@aba',           // Your Bakong account
    'merchantname' => 'My Electronics',     // Your business name
    'currency' => 'USD'                     // Optional: USD or KHR
]);

$response = $khqrController->create($request);
$data = $response->getData(true);

// Display QR code to customer
return view('payment', [
    'qrCode' => $data['qr'],           // Base64 SVG image
    'md5' => $data['md5'],             // For status checking
    'amount' => $data['amount'],
    'transactionId' => $data['tran']
]);
```

---

## Need Help?

- Check: `ACCOUNT_NOT_FOUND_EXPLANATION.md`
- Check: `KHQR_FIX_EXPLANATION.md`
- Check: `HOW_TO_FIX_ACCOUNT_ERROR.txt`

---

Generated: October 18, 2025
