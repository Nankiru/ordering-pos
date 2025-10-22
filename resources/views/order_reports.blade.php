<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Sales Reports ({{ ucfirst($period) }})</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Return to Dashboard</a>
    </div>
    <form method="GET" action="{{ route('admin.reports') }}" class="mb-4">
        <select name="period" class="form-select d-inline-block w-auto">
            <option value="daily" @if($period=='daily') selected @endif>Daily</option>
            <option value="weekly" @if($period=='weekly') selected @endif>Weekly</option>
            <option value="monthly" @if($period=='monthly') selected @endif>Monthly</option>
        </select>
        <button type="submit" class="btn btn-primary">View</button>
    </form>
    <div class="mb-4">
        <h4>Total Sales: ${{ $total }}</h4>
    </div>
    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
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
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
