<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">SadTime Admin</a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('locale.set', ['locale' => 'en']) }}" class="btn btn-sm btn-outline-secondary">EN</a>
            <a href="{{ route('locale.set', ['locale' => 'km']) }}" class="btn btn-sm btn-outline-secondary">KM</a>
            <a href="{{ route('locale.set', ['locale' => 'zh']) }}" class="btn btn-sm btn-outline-secondary">中文</a>
        </div>
    </div>
    </nav>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Orders</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Return to Dashboard</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->item->name ?? '' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ $order->total }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                        @csrf
                        @php
                            $allStatuses = ['Pending','Confirmed','Cooking','Out for Delivery','Delivered'];
                            $isBeer = strtolower(optional(optional($order->item)->category)->name ?? '') === 'beer';
                            $statuses = $isBeer ? array_values(array_filter($allStatuses, function ($s) { return $s !== 'Cooking'; })) : $allStatuses;
                        @endphp
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @if($order->status == $status) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>{{ $order->created_at->format('d-M-Y') }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" onsubmit="return confirm('Delete Order #{{ $order->id }}?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
