{{-- filepath: resources/views/admin/products/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Products Management')
@section('page-title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Products Management</h4>
        <small class="text-muted">Manage your product inventory</small>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Product
    </a>
</div>

<!-- Filters & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Product name, SKU..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products ?? [] as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->main_image_url }}"
                                     alt="{{ $product->name }}" class="rounded" width="60" height="60">
                            </td>
                            <td>
                                <div>
                                    <h6 class="mb-1">{{ Str::limit($product->name, 40) }}</h6>
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    @if($product->is_featured)
                                        <span class="badge bg-warning ms-1">Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-primary">{{ number_format($product->price, 0) }}đ</strong>
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                        <br><small class="text-muted text-decoration-line-through">{{ number_format($product->compare_price, 0) }}đ</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $stockClass = match(true) {
                                        $product->stock <= 0 => 'danger',
                                        $product->stock <= 5 => 'warning',
                                        default => 'success'
                                    };
                                    $stockText = match(true) {
                                        $product->stock <= 0 => 'Out of Stock',
                                        $product->stock <= 5 => 'Low Stock',
                                        default => 'In Stock'
                                    };
                                @endphp
                                <div>
                                    <span class="badge bg-{{ $stockClass }}">{{ $stockText }}</span>
                                    <small class="d-block text-muted">Qty: {{ $product->stock }}</small>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($product->status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'draft' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($product->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="text-warning me-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= ($product->rating ?? 0))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">({{ $product->review_count ?? 0 }})</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteProduct({{ $product->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No products found</h5>
                                    <p class="text-muted">Start by adding your first product</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($products) && $products->hasPages())
            <div class="card-footer">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Delete product
    function deleteProduct(id) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/products/${id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Bulk actions
    function bulkAction(action) {
        const selected = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select products first');
            return;
        }

        if (confirm(`Are you sure you want to ${action} ${selected.length} product(s)?`)) {
            // Implement bulk action logic
            console.log(`Bulk ${action}:`, selected);
        }
    }

</script>
@endpush

@push('styles')
<style>
    .empty-state {
        padding: 3rem 1rem;
    }

    .table td {
        vertical-align: middle;
    }

    .product-checkbox {
        cursor: pointer;
    }

    .btn-group .btn {
        border-radius: 0.375rem;
        margin-right: 2px;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endpush
