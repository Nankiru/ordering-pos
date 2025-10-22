<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background:#f7f8fb; }
        .success-card { border:0; border-radius:.75rem; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
    </style>
    </head>
<body>
<div class="container py-5" style="max-width:720px;">
    <div class="text-center mb-4">
        <div class="display-6 text-success"><i class="fa-solid fa-circle-check me-2"></i>Order placed</div>
        <div class="text-muted">We’re preparing your food.</div>
    </div>
    @if($orders->count())
        <div class="success-card bg-white p-3 mb-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="fw-semibold">Order IDs</div>
                    <div>{{ $orders->pluck('id')->join(', ') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="fw-semibold">Total Amount</div>
                    <div>${{ number_format($orders->sum('total'), 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="fw-semibold">ETA</div>
                    <div>~{{ $etaMinutes }} minutes</div>
                </div>
            </div>
        </div>
        <a href="{{ route('shop.feedback') }}" class="btn btn-outline-primary">Leave Feedback</a>
        <a href="{{ route('shop.menu') }}" class="btn btn-primary ms-2">Back to Menu</a>
    @else
        <div class="alert alert-warning">No orders found.</div>
        <a href="{{ route('shop.menu') }}" class="btn btn-primary">Back to Menu</a>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


