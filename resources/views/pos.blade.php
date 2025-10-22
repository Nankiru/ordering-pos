<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        .scrollbar-thin{scrollbar-width:thin}
        .scrollbar-thin::-webkit-scrollbar{height:8px;width:8px}
        .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}
    </style>
</head>
<body class="bg-[#F5F6F8] text-[#0f172a]">
<div class="min-h-screen p-4">
    <div class="max-w-[1400px] mx-auto bg-white rounded-xl shadow border border-gray-100">
        <div class="flex">
            <aside class="w-60 border-r border-gray-100 p-4 hidden md:block">
                <div class="font-semibold text-orange-600 mb-4">RestroBit</div>
                <nav class="space-y-1 text-sm">
                    <a class="block px-3 py-2 rounded-md bg-gray-100" href="#">Pos</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-gray-50" href="#">Table</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-gray-50" href="#">Reservations</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-gray-50" href="#">Payments</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-gray-50" href="#">Reports</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-gray-50" href="#">Setting</a>
                </nav>
            </aside>

            <main class="flex-1 p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-lg font-semibold">Point of Sale (POS)</div>
                    <div class="flex gap-2 items-center">
                        <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex px-3 py-2 rounded-md border">Dashboard</a>
                        <a href="{{ route('shop.menu') }}" class="px-3 py-2 rounded-md bg-orange-500 text-white">New</a>
                        <div class="ml-2">
                            <a href="{{ route('shop.cart') }}" class="px-3 py-1 rounded-md border bg-white">Cart <span id="cart-count-badge" class="ml-1 font-semibold">{{ array_sum($cart) }}</span></a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <div class="flex gap-2 mb-3">
                            <div class="flex-1">
                                <input class="w-full border rounded-md px-3 py-2" placeholder="Search in products" />
                            </div>
                            <select class="border rounded-md px-3 py-2">
                                <option>All Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 overflow-x-auto scrollbar-thin pb-2">
                            <button class="px-3 py-1.5 rounded-md bg-orange-500 text-white text-sm">Show All</button>
                            @foreach($categories as $cat)
                                <button class="px-3 py-1.5 rounded-md bg-gray-100 text-sm">{{ $cat->name }}</button>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 mt-3">
                            @foreach($items as $item)
                                <form method="post" action="{{ route('shop.cart.add', $item->id) }}" class="border rounded-xl bg-white p-3 shadow-sm">
                                    @csrf
                                    @if($item->image_url)
                                        <img src="{{ $item->image_url }}" class="w-full h-24 object-cover rounded-md mb-3" alt="{{ $item->name }}">
                                    @endif
                                    <div class="text-sm font-medium">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-500 mb-2">{{ $item->category?->name }}</div>
                                    <div class="flex items-center justify-between">
                                        <div class="font-semibold">${{ number_format($item->price, 2) }}</div>
                                        <button type="button" class="h-8 px-2 rounded-md bg-orange-500 text-white text-xs js-add-to-cart">Add</button>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <aside class="lg:col-span-1">
                        <div class="border rounded-xl bg-white p-3 shadow-sm">
                            <div class="flex items-center gap-2 mb-3">
                                <input class="w-full border rounded-md px-3 py-2" placeholder="Search in cart">
                            </div>
                            <div class="text-sm font-semibold mb-2">Current Order</div>
                            <div class="space-y-2 max-h-[420px] overflow-auto pr-1 scrollbar-thin">
                                @php $subtotal = 0; @endphp
                                @foreach($cart as $id => $qty)
                                    @php $p = $items->firstWhere('id', (int)$id); if(!$p) continue; $line = $p->price * $qty; $subtotal += $line; @endphp
                                    <div class="flex items-center justify-between p-2 border rounded-md">
                                        <div>
                                            <div class="text-sm font-medium">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500">${{ number_format($p->price,2) }} × {{ $qty }}</div>
                                        </div>
                                        <div class="font-semibold">${{ number_format($line,2) }}</div>
                                    </div>
                                @endforeach
                                @if(empty($cart))
                                    <div class="text-sm text-gray-500">No items yet.</div>
                                @endif
                            </div>

                            <div class="mt-3 border-t pt-3 space-y-1 text-sm">
                                <div class="flex justify-between"><span>Sub total</span><span>${{ number_format($subtotal,2) }}</span></div>
                                <div class="flex justify-between"><span>Discount</span><span>$0.00</span></div>
                                <div class="flex justify-between font-semibold text-lg"><span>Total</span><span>${{ number_format($subtotal,2) }}</span></div>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <button type="button" onclick="document.getElementById('pay').showModal()" class="col-span-2 h-10 rounded-md bg-orange-600 text-white">Bill & Payment</button>
                                <a href="{{ route('shop.cart') }}" class="h-10 rounded-md bg-gray-100 flex items-center justify-center">Draft</a>
                                <button class="h-10 rounded-md bg-green-600 text-white">Bill & Print</button>
                            </div>
                        </div>
                    </aside>
                </div>
            </main>
        </div>
    </div>
</div>

<dialog id="pay" class="rounded-xl p-0 w-[420px] max-w-[95vw]">
    <form method="dialog" class="p-4 border-b flex items-center justify-between">
        <div class="font-semibold">Collect Payment</div>
        <button class="px-2 py-1 text-sm rounded-md border">Close</button>
    </form>
    <div class="p-4 space-y-3">
        <div class="flex gap-2">
            <button class="flex-1 h-9 rounded-md bg-gray-900 text-white text-sm">Full Payment</button>
            <button class="flex-1 h-9 rounded-md bg-gray-100 text-sm">Split Bill</button>
        </div>
        <div class="grid grid-cols-4 gap-2 text-sm">
            <button class="h-9 rounded-md border">$5</button>
            <button class="h-9 rounded-md border">$10</button>
            <button class="h-9 rounded-md border">$20</button>
            <button class="h-9 rounded-md border">$50</button>
        </div>
        <div class="text-right font-semibold text-lg">${{ number_format($subtotal ?? 0,2) }}</div>
        <button class="w-full h-10 rounded-md bg-orange-600 text-white">Complete Payment</button>
    </div>
</dialog>

</body>
</html>


