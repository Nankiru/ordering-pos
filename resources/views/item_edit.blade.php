<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f7f8fb; }
        .card { border:0; border-radius:.75rem; box-shadow:0 8px 24px rgba(0,0,0,.06); }
    </style>
</head>
<body>
<div class="container py-5" style="max-width:720px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit Item</h2>
        <a href="{{ route('admin.menu') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="card p-3 bg-white">
    <form method="POST" action="{{ route('admin.items.update', $item->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Current Image</label>
            <div>
                @if($item->image)
                    <img src="{{ asset('uploads/items/' . $item->image) }}" alt="" style="width:160px; height:90px; object-fit:cover;" class="mb-2">
                @else
                    <div class="bg-secondary" style="width:160px; height:90px;"></div>
                @endif
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Replace Image (optional)</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                    <option value="{{ $cat->id }}" @if(old('category_id', $item->category_id) == $cat->id) selected @endif>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $item->price) }}" required>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.menu') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
    </div>
</div>
</body>
</html>
