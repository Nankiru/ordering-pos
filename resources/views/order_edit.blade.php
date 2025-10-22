<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Order #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>
<body>
<div class="container py-4" style="max-width:640px;">
    <h4 class="mb-3">Edit Order #{{ $order->id }}</h4>
    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Customer name</label>
            <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Item</label>
            <select class="form-select" name="item_id" required>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" @if($order->item_id==$item->id) selected @endif>{{ $item->name }} (${{ number_format($item->price,2) }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-control" name="quantity" min="1" value="{{ old('quantity', $order->quantity) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @php $allStatuses=['Pending','Confirmed','Cooking','Out for Delivery','Delivered']; @endphp
                @foreach($allStatuses as $s)
                    <option value="{{ $s }}" @if($order->status===$s) selected @endif>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


