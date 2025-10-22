<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users & Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ ($mode ?? 'users') === 'admins' ? 'Admins' : 'Users' }}</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Return to Dashboard</a>
    </div>

    @if(($mode ?? 'users') === 'admins')
        <div class="card">
            <div class="card-header">Admins (latest)</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
                    <tbody>
                    @forelse($admins as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->email }}</td>
                            <td>{{ $a->created_at }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No admins.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">Users (latest)</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
                    <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->created_at }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No users.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
</body>
</html>


