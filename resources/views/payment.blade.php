<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - {{ env('APP_NAME', 'Sopheanan Khem') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 283; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 min-h-screen animate-gradient">

    <!-- Header -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('shop.menu') }}" class="text-white hover:text-blue-300 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Back to Shop</span>
            </a>
            <div class="text-white text-sm">
                <i class="fas fa-shield-alt text-green-400"></i>
                <span class="ml-1">Secure Payment</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 max-w-6xl">

        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl shadow-2xl p-6 mb-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-qrcode mr-2"></i>KHQR Payment
                    </h1>
                    <p class="text-blue-100">Scan the QR code with your banking app to complete payment</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 text-center border border-white/30">
                    <div class="text-sm text-blue-100 mb-1">Amount</div>
                    <div class="text-3xl font-bold">${{ $amount }}</div>
                    @if(isset($discountAmount) && $discountAmount > 0)
                    <div class="text-xs text-green-200 mt-1">
                        <i class="fas fa-tag"></i> Saved ${{ number_format($discountAmount, 2) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Payment Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid lg:grid-cols-5 gap-0">

                <!-- QR Code Section -->
                <div class="lg:col-span-3 p-8">

                    <!-- Timer -->
                    <div class="flex items-center justify-center mb-6">
                        <div class="relative">
                            <svg class="w-16 h-16 transform -rotate-90">
                                <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none" />
                                <circle id="timer-circle" cx="32" cy="32" r="28" stroke="#3b82f6" stroke-width="4" fill="none"
                                    stroke-dasharray="175.93" stroke-dashoffset="0" class="transition-all duration-1000" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span id="timer-text" class="text-lg font-bold text-blue-600">10:00</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-500">Time Remaining</div>
                            <div class="text-xs text-gray-400">Payment will expire</div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="flex justify-center mb-6">
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-opacity"></div>
                            <div class="relative bg-white p-6 rounded-2xl shadow-xl">
                                <img src="{{ $qrUrl }}" alt="KHQR Payment Code" class="w-72 h-72 object-contain">
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                                    <i class="fas fa-check-circle"></i> Active
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500 mb-1">Customer</div>
                                <div class="font-semibold text-gray-800">{{ $customerName }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 mb-1">Transaction ID</div>
                                <div class="font-mono text-xs text-gray-800 break-all">{{ $md5 }}</div>
                            </div>
                            @if(isset($appliedPromo) && $appliedPromo !== '')
                            <div class="col-span-2">
                                <div class="text-gray-500 mb-1">Promo Applied</div>
                                <div class="font-semibold text-green-600">
                                    <i class="fas fa-ticket-alt"></i> {{ $appliedPromo }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button id="btn-reload" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl active:scale-95">
                            <i class="fas fa-sync-alt mr-2"></i>Reload QR
                        </button>
                        <button id="btn-cancel" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl active:scale-95">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                    </div>

                    <!-- Status Alert -->
                    <div id="status" class="mt-6 p-4 rounded-xl bg-blue-50 border-l-4 border-blue-500 flex items-center gap-3">
                        <i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i>
                        <div>
                            <div class="font-semibold text-blue-800">Waiting for payment...</div>
                            <div class="text-sm text-blue-600">Checking payment status automatically</div>
                        </div>
                    </div>

                    @if(app()->environment('local'))
                    <!-- Test Payment Simulator (Development Only) -->
                    <div class="mt-4 p-4 rounded-xl bg-purple-50 border border-purple-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-flask text-purple-600 text-lg mt-1"></i>
                            <div class="flex-1">
                                <div class="font-semibold text-purple-800 mb-1">Testing Mode</div>
                                <div class="text-sm text-purple-600 mb-3">Use the simulator to confirm payment instantly</div>
                                <a href="{{ route('khqr.simulator') }}?md5={{ $md5 }}" target="_blank"
                                   class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-external-link-alt"></i>
                                    Open Payment Simulator
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Instructions Section -->
                <div class="lg:col-span-2 bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        How to Pay
                    </h3>

                    <!-- Steps -->
                    <ol class="space-y-4 mb-8">
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center font-bold">1</div>
                            <div class="flex-1">
                                <div class="font-semibold mb-1">Open Banking App</div>
                                <div class="text-sm text-gray-300">Launch your mobile banking application</div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center font-bold">2</div>
                            <div class="flex-1">
                                <div class="font-semibold mb-1">Scan QR Code</div>
                                <div class="text-sm text-gray-300">Choose "Scan" or "KHQR" and point your camera at the code</div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center font-bold">3</div>
                            <div class="flex-1">
                                <div class="font-semibold mb-1">Confirm Payment</div>
                                <div class="text-sm text-gray-300">Verify the amount (${{ $amount }}) and approve the transaction</div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center font-bold">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold mb-1">Done!</div>
                                <div class="text-sm text-gray-300">Your order will be confirmed automatically</div>
                            </div>
                        </li>
                    </ol>

                    <!-- Supported Banks -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-6 border border-white/20">
                        <div class="text-sm font-semibold mb-2">Supported Banks</div>
                        <div class="text-xs text-gray-300">ABA, ACLEDA, Canadia, Wing, TrueMoney, Pi Pay, and all KHQR-compatible banks</div>
                    </div>

                    <!-- Help -->
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-lightbulb text-yellow-400 mt-1"></i>
                            <div class="text-sm text-yellow-100">
                                <strong>Having trouble?</strong><br>
                                • Increase screen brightness<br>
                                • Try reloading the QR code<br>
                                • Ensure stable internet connection<br>
                                • Don't close this page until confirmed
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="text-center mt-6 text-white/70 text-sm flex items-center justify-center gap-2">
            <i class="fas fa-lock"></i>
            <span>Your payment is processed securely through NBC's KHQR system</span>
        </div>
    </div>

<script>
const checkUrl = "{{ route('shop.pay.check', ['md5' => $md5]) }}";
let attempts = 0;
const maxAttempts = 120; // 10 minutes at 5s intervals
let cancelled = false;
let timeoutSeconds = 600; // 10 minutes

// Timer countdown
const timerInterval = setInterval(() => {
    if (cancelled) {
        clearInterval(timerInterval);
        return;
    }

    timeoutSeconds--;
    const minutes = Math.floor(timeoutSeconds / 60);
    const seconds = timeoutSeconds % 60;
    document.getElementById('timer-text').textContent =
        `${minutes}:${seconds.toString().padStart(2, '0')}`;

    // Update circle progress
    const circle = document.getElementById('timer-circle');
    const progress = (timeoutSeconds / 600) * 175.93;
    circle.style.strokeDashoffset = 175.93 - progress;

    // Change color as time runs out
    if (timeoutSeconds <= 60) {
        circle.setAttribute('stroke', '#ef4444'); // red
        document.getElementById('timer-text').classList.add('text-red-600');
        document.getElementById('timer-text').classList.remove('text-blue-600');
    } else if (timeoutSeconds <= 180) {
        circle.setAttribute('stroke', '#f59e0b'); // orange
        document.getElementById('timer-text').classList.add('text-orange-600');
        document.getElementById('timer-text').classList.remove('text-blue-600');
    }

    if (timeoutSeconds <= 0) {
        clearInterval(timerInterval);
        cancelled = true;
        showStatus('error', 'Payment Expired', 'The payment time limit has been reached. Please return to checkout to try again.');
        setTimeout(() => {
            window.location.href = "{{ route('shop.checkout') }}";
        }, 3000);
    }
}, 1000);

// Payment verification polling
async function poll() {
    if (cancelled || timeoutSeconds <= 0) return;

    try {
        const resp = await fetch(checkUrl);
        const data = await resp.json();

        if (data.paid) {
            cancelled = true;
            clearInterval(timerInterval);
            showStatus('success', 'Payment Confirmed!', 'Your payment has been received. Redirecting to confirmation...');

            // Submit order
            setTimeout(() => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('shop.place') }}";

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);

                const nameI = document.createElement('input');
                nameI.type = 'hidden';
                nameI.name = 'customer_name';
                nameI.value = '{{ $customerName }}';
                form.appendChild(nameI);

                const addrI = document.createElement('input');
                addrI.type = 'hidden';
                addrI.name = 'address';
                addrI.value = '{{ $address }}';
                form.appendChild(addrI);

                const pm = document.createElement('input');
                pm.type = 'hidden';
                pm.name = 'payment_method';
                pm.value = 'online';
                form.appendChild(pm);

                @if(isset($appliedPromo) && $appliedPromo !== '')
                const promoI = document.createElement('input');
                promoI.type = 'hidden';
                promoI.name = 'applied_promo';
                promoI.value = '{{ $appliedPromo }}';
                form.appendChild(promoI);
                @endif

                document.body.appendChild(form);
                form.submit();
            }, 1500);
            return;
        }
    } catch (e) {
        console.error('Payment check error:', e);
        showStatus('warning', 'Connection Issue', 'Retrying connection... Please keep this page open.');
    }

    attempts++;
    if (attempts < maxAttempts && !cancelled) {
        setTimeout(poll, 5000);
    } else if (attempts >= maxAttempts) {
        showStatus('error', 'Payment Timeout', 'Unable to verify payment. You can reload the page or return to checkout.');
    }
}

function showStatus(type, title, message) {
    const statusDiv = document.getElementById('status');
    const icons = {
        success: '<i class="fas fa-check-circle text-green-500 text-xl"></i>',
        error: '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>',
        warning: '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>',
        info: '<i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i>'
    };
    const colors = {
        success: 'bg-green-50 border-green-500',
        error: 'bg-red-50 border-red-500',
        warning: 'bg-yellow-50 border-yellow-500',
        info: 'bg-blue-50 border-blue-500'
    };
    const textColors = {
        success: 'text-green-800',
        error: 'text-red-800',
        warning: 'text-yellow-800',
        info: 'text-blue-800'
    };

    statusDiv.className = `mt-6 p-4 rounded-xl ${colors[type]} border-l-4 flex items-center gap-3`;
    statusDiv.innerHTML = `
        ${icons[type]}
        <div>
            <div class="font-semibold ${textColors[type]}">${title}</div>
            <div class="text-sm ${textColors[type].replace('800', '600')}">${message}</div>
        </div>
    `;
}

// Start polling after 5 seconds
setTimeout(poll, 5000);

// Button handlers
document.getElementById('btn-reload').addEventListener('click', function() {
    location.reload();
});

document.getElementById('btn-cancel').addEventListener('click', function() {
    cancelled = true;
    clearInterval(timerInterval);
    if (confirm('Are you sure you want to cancel this payment? You will be redirected to checkout.')) {
        window.location.href = "{{ route('shop.checkout') }}";
    } else {
        cancelled = false;
    }
});

// Prevent accidental page closure
window.addEventListener('beforeunload', function(e) {
    if (!cancelled && timeoutSeconds > 0) {
        e.preventDefault();
        e.returnValue = 'Payment is in progress. Are you sure you want to leave?';
        return e.returnValue;
    }
});
</script>
</body>
</html>



