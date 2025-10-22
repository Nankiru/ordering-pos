<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg,#6a11cb,#2575fc); min-height:100vh; }
        .card { border-radius: .75rem; box-shadow: 0 6px 24px rgba(0,0,0,.08); }
    </style>
    </head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg,#6366f1,#6dd5ed);">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-chart-line"></i> Sales Reports</a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light">Return to Dashboard</a>
        </div>
    </div>
  </nav>

<div class="container py-4">
    <div class="mb-4">
        <h2 class="text-white">Monthly Sales</h2>
        @if($current)
            <div class="text-white-50">Current month total: <strong>${{ number_format($current->total_amount, 2) }}</strong> ({{ $current->total_orders }} orders)</div>
        @endif
    </div>

    <div class="card p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Year</th>
                    <th>Month</th>
                    <th class="text-end">Total Orders</th>
                    <th class="text-end">Total Amount</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td>{{ $r->year }}</td>
                        <td>{{ DateTime::createFromFormat('!m', $r->month)->format('F') }}</td>
                        <td class="text-end">{{ $r->total_orders }}</td>
                        <td class="text-end">${{ number_format($r->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No data yet. Run: php artisan sales:aggregate-month</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


