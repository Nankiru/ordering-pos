<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background:#f7f8fb; }
        .card { border:0; border-radius:.75rem; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
    </style>
    </head>
<body>
<div class="container py-4" style="max-width:640px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="fa-regular fa-comment-dots me-2"></i>Feedback</h3>
        <a href="{{ route('shop.menu') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="card p-3 bg-white">
        <form method="POST" action="{{ route('shop.feedback.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name (optional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


