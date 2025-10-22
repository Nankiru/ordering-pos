<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHQR Payment Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen">

    <div class="container mx-auto px-4 py-12 max-w-4xl">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <i class="fas fa-mobile-alt mr-3"></i>KHQR Payment Simulator
            </h1>
            <p class="text-blue-200">Test payment confirmations for development</p>
        </div>

        <!-- Instructions -->
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 mb-8">
            <div class="flex items-start gap-4">
                <i class="fas fa-info-circle text-yellow-400 text-2xl mt-1"></i>
                <div class="text-yellow-100">
                    <h3 class="font-bold mb-2">How to use:</h3>
                    <ol class="list-decimal list-inside space-y-1 text-sm">
                        <li>Go through checkout and reach the payment page with QR code</li>
                        <li>Copy the transaction MD5 hash from the URL or page</li>
                        <li>Paste it below and click "Simulate Payment"</li>
                        <li>The payment page will automatically detect and confirm the order</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Simulator Form -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-check-circle mr-2"></i>Simulate Payment Confirmation
                </h2>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Transaction MD5 Hash
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="md5-input"
                        placeholder="Enter MD5 hash from payment page..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all font-mono text-sm"
                    >
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-lightbulb text-yellow-500"></i>
                        Tip: Find the MD5 in the URL query parameter or in the browser console
                    </p>
                </div>

                <button
                    id="simulate-btn"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas fa-check-circle mr-2"></i>
                    <span id="btn-text">Simulate Payment</span>
                </button>

                <!-- Response Display -->
                <div id="response" class="mt-6 hidden"></div>
            </div>
        </div>

        <!-- Active Transactions -->
        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 mt-8">
            <h3 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-list mr-2"></i>Recent Transactions
            </h3>
            <div id="transactions" class="text-white/70 text-sm">
                Loading...
            </div>
        </div>

        <!-- API Documentation -->
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 mt-8">
            <h3 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-code mr-2"></i>API Endpoints
            </h3>
            <div class="space-y-3 text-sm">
                <div class="bg-black/30 rounded-lg p-4">
                    <div class="text-green-400 font-mono mb-1">GET /api/khqr/create</div>
                    <div class="text-gray-300">Generate new KHQR payment code</div>
                    <div class="text-gray-400 text-xs mt-2">Params: amount, bakongid, merchantname</div>
                </div>
                <div class="bg-black/30 rounded-lg p-4">
                    <div class="text-blue-400 font-mono mb-1">GET /api/khqr/check_by_md5</div>
                    <div class="text-gray-300">Check payment status</div>
                    <div class="text-gray-400 text-xs mt-2">Params: md5, bakongid</div>
                </div>
                <div class="bg-black/30 rounded-lg p-4">
                    <div class="text-purple-400 font-mono mb-1">POST /api/khqr/simulate-payment</div>
                    <div class="text-gray-300">Simulate payment confirmation (dev only)</div>
                    <div class="text-gray-400 text-xs mt-2">Body: md5</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const md5Input = document.getElementById('md5-input');
        const simulateBtn = document.getElementById('simulate-btn');
        const btnText = document.getElementById('btn-text');
        const responseDiv = document.getElementById('response');

        simulateBtn.addEventListener('click', async function() {
            const md5 = md5Input.value.trim();

            if (!md5) {
                showResponse('error', 'Please enter an MD5 hash');
                return;
            }

            // Disable button
            simulateBtn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            try {
                const response = await fetch('/api/khqr/simulate-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ md5 })
                });

                const data = await response.json();

                if (data.success) {
                    showResponse('success',
                        `Payment simulated successfully!<br>
                        <span class="text-sm">Transaction: ${data.md5}</span><br>
                        <span class="text-sm">Status: ${data.status}</span>`
                    );
                    md5Input.value = '';
                } else {
                    showResponse('error', data.message || 'Simulation failed');
                }
            } catch (error) {
                showResponse('error', 'Network error: ' + error.message);
            } finally {
                simulateBtn.disabled = false;
                btnText.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Simulate Payment';
            }
        });

        function showResponse(type, message) {
            const colors = {
                success: 'bg-green-50 border-green-500 text-green-800',
                error: 'bg-red-50 border-red-500 text-red-800'
            };
            const icons = {
                success: '<i class="fas fa-check-circle text-green-500 text-xl"></i>',
                error: '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>'
            };

            responseDiv.className = `p-4 rounded-lg border-l-4 ${colors[type]} flex items-start gap-3`;
            responseDiv.innerHTML = `
                ${icons[type]}
                <div class="flex-1">${message}</div>
            `;
            responseDiv.classList.remove('hidden');

            // Auto-hide after 5 seconds for success
            if (type === 'success') {
                setTimeout(() => {
                    responseDiv.classList.add('hidden');
                }, 5000);
            }
        }

        // Auto-fill from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        const md5FromUrl = urlParams.get('md5');
        if (md5FromUrl) {
            md5Input.value = md5FromUrl;
        }
    </script>
</body>
</html>
