<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #eef2ff, #f7f8fb); min-height: 100vh; display:flex; align-items:center; }
        .auth-card { border:0; border-radius: 1rem; box-shadow: 0 12px 28px rgba(0,0,0,.08); }
        .brand { font-weight: 700; }
    </style>
    </head>
<body>
<div class="container py-5" style="max-width:460px;">
    <div class="text-center mb-3">
        <div class="brand h4 mb-1"><i class="fa-solid fa-utensils me-1"></i> SadTime</div>
        <div class="text-muted">Welcome back</div>
    </div>
    <div class="card auth-card p-4 bg-white">
        <h5 class="mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</h5>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('customer.login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary">Login</button>
                <a href="{{ route('customer.register.form') }}" class="btn btn-outline-secondary">Create an account</a>
            </div>
        </form>
    </div>
    <div class="text-center mt-3">
        <a href="{{ route('shop.menu') }}" class="text-decoration-none">Back to shop</a>
    </div>
    </div>
</body>
</html>


