<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Menu — SadTime Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Cinzel:wght@400..900&family=Dangrek&family=Google+Sans+Code:ital,wght@0,300..800;1,300..800&family=Hanuman:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libertinus+Serif:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Noto+Sans+Khmer:wght@100..900&family=Noto+Serif+Khmer:wght@100..900&family=Suwannaphum:wght@100;300;400;700;900&display=swap');

        body {
            background: linear-gradient(135deg, #6366f1 0%, #6dd5ed 100%);
            min-height: 100vh;
        }

        .stat-card {
            border-radius: .75rem;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
        }

        .nav-brand {
            font-weight: 700;
        }

        .suwannaphum-light {
            font-family: "Suwannaphum", serif;
            font-weight: 300;
            font-style: normal;
        }
        /* Small visual polish */
        .sidebar-card { height: 100%; }
        .category-badge { min-width: 36px; }
        .table-actions .btn { margin-left: .25rem; }
        .brand { font-weight: 700; letter-spacing: .4px; }
        .search-input { max-width: 360px; }
    </style>
</head>
<body class="bg-light suwannaphum-light">
    <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/img/logo.png') }}"
                    alt="SadTime" class="w-20 h-20" /></a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="{{ route('shop.menu') }}" class="btn btn-sm btn-outline-secondary">{{ __('Return to Shop') }}</a>
                @include('components.lang-switch')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary ms-2">Dashboard</a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display:inline">
                    @csrf
                    <button class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.manage_menu') }}</h1>
                <small class="text-muted">{{ __('messages.manage_menu_sub') }}</small>
            </div>
            <div class="d-flex">
                <input id="globalSearch" class="form-control form-control-sm search-input me-2" placeholder="{{ __('messages.search_placeholder') }}">
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">{{ __('messages.new_category') }}</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">{{ __('messages.new_item') }}</button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card sidebar-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('messages.categories') }}</h5>
                            <small class="text-muted">{{ isset($categories) ? count($categories) : 0 }} total</small>
                        </div>
                        <div class="list-group">
                            @if(isset($categories) && count($categories))
                                @foreach($categories as $category)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($category->image)
                                                <img src="{{ asset('uploads/category/' . $category->image) }}" alt="" class="rounded me-2" style="width:48px; height:48px; object-fit:cover;">
                                            @else
                                                <div class="bg-secondary rounded me-2" style="width:48px; height:48px;"></div>
                                            @endif
                                            <div>
                                                <strong>{{ $category->name }}</strong>
                                                @if($category->description)
                                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($category->description, 60) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning me-1">Edit</a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" style="display:inline" onsubmit="return confirm('Delete category «'+ '{{ $category->name }}' +'»?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    {{ __('messages.no_categories') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('messages.items') }}</h5>
                            <div class="text-muted small">{{ isset($items) ? count($items) : 0 }} items</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.image') }}</th>
                                        <th>{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.category') }}</th>
                                        <th>{{ __('messages.price_usd') }}</th>
                                        <th>{{ __('messages.price_khr') }}</th>
                                        <th class="text-center">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($items) && count($items))
                                        @foreach($items as $item)
                                            <tr>
                                                <td>
                                                    @if($item->image_url)
                                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="rounded" style="width:48px; height:48px; object-fit:cover;">
                                                    @else
                                                        <div class="bg-secondary rounded" style="width:48px; height:48px;"></div>
                                                    @endif
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->category?->name ?? '—' }}</td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td>{{ number_format($item->price * 4000, 2) }} ៛</td>
                                                <td class="text-end table-actions">
                                            <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-sm btn-outline-warning">{{ __('messages.edit') }}</a>
                                                    <form method="POST" action="{{ route('admin.items.update', $item->id) }}" style="display:inline;">
                                                        {{-- optional: keep update route for quick inline actions; real delete should be implemented separately --}}
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.items.update', $item->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.items.destroy', $item->id) }}" style="display:inline" onsubmit="return confirm('Delete item «'+ '{{ $item->name }}' +'»?');">
                                                        @csrf
                                                        @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_items') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="category_name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_description" class="form-label">Description</label>
                            <textarea class="form-control" id="category_description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (optional)</label>
                            <input type="file" name="image" id="add_category_image" accept="image/*" class="form-control">
                            <img id="add_category_image_preview" src="" alt="" class="mt-2 rounded" style="max-width:120px; max-height:80px; display:none; object-fit:cover;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemLabel">Create Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select...</option>
                                    @if(isset($categories) && count($categories))
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price') ?? '0.00' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" id="add_item_image" accept="image/*" class="form-control">
                                <img id="add_item_image_preview" src="" alt="" class="mt-2 rounded" style="max-width:120px; max-height:80px; display:none; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure protected page is not shown from bfcache after logout
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
        // Simple client-side filter for items table
        document.addEventListener('DOMContentLoaded', function () {
            var search = document.getElementById('globalSearch');
            if (!search) return;
            search.addEventListener('input', function () {
                var q = this.value.toLowerCase().trim();
                var rows = document.querySelectorAll('#itemsTable tbody tr');
                rows.forEach(function (r) {
                    var text = r.innerText.toLowerCase();
                    r.style.display = text.indexOf(q) === -1 ? 'none' : '';
                });
            });
        });
        // Add Item modal image preview
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('add_item_image');
            var preview = document.getElementById('add_item_image_preview');
            if (!input || !preview) return;
            input.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (!file) { preview.style.display = 'none'; preview.src = ''; return; }
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
