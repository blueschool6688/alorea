@extends('admin.layout')

@section('title', 'Categories Management')
@section('page-title', 'Quản lý danh mục')

@push('styles')
<style>
    .category-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e9ecef;
    }
    .status-badge.active {
        background: #212529 !important;
    }
    .status-badge.inactive {
        background: #6c757d !important;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        margin: 0 0.125rem;
    }
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
    }
    .stats-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        transition: transform 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .category-tree {
        padding-left: 0;
    }
    .category-tree .category-child {
        padding-left: 2rem;
        border-left: 2px solid #dee2e6;
        position: relative;
    }
    .category-tree .category-child::before {
        content: '';
        position: absolute;
        left: -1px;
        top: 0;
        bottom: 50%;
        border-left: 2px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Danh sách danh mục</h4>
        <p class="text-muted mb-0">Quản lý danh mục sản phẩm nước hoa</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">
            <i class="fas fa-plus me-2"></i>Thêm danh mục
        </a>
    </div>
</div>

<!-- Filters & Search -->
<div class="card filter-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0"
                           name="search" placeholder="Tên danh mục..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Danh mục cha</label>
                <select name="parent_id" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="null" {{ request('parent_id') === 'null' ? 'selected' : '' }}>
                        Chỉ danh mục cha
                    </option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}"
                                {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-filter me-1"></i>Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body p-0">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th width="70">Hình ảnh</th>
                            <th>Tên danh mục</th>
                            <th width="150">Danh mục cha</th>
                            <th width="100">Sản phẩm</th>
                            <th width="100">Trạng thái</th>
                            <th width="120">Ngày tạo</th>
                            <th width="150" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="category-tree">
                        @foreach($categories as $category)
                            <tr class="{{ $category->parent_id ? 'category-child' : '' }}">
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input category-checkbox"
                                               type="checkbox" value="{{ $category->id }}">
                                    </div>
                                </td>
                                <td>
                                    <img width="200" height="200" src="{{ $category->image_url }}"
                                         alt="{{ $category->name }}"
                                         class="category-image">
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('admin.categories.show', $category) }}"
                                           class="fw-bold text-decoration-none text-dark">
                                            @if($category->parent_id)
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-1"></i>
                                            @endif
                                            {{ $category->name }}
                                        </a>
                                        @if($category->is_featured)
                                            <span class="badge bg-warning text-dark ms-2">Nổi bật</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-link me-1"></i>{{ $category->slug }}
                                    </div>
                                    @if($category->description)
                                        <small class="text-muted d-block">
                                            {{ Str::limit($category->description, 60) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($category->parent)
                                        <span class="badge bg-secondary">
                                            {{ $category->parent->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $category->products_count }}</span>
                                        @if($category->children->count() > 0)
                                            <span class="badge bg-info ms-2" title="Có {{ $category->children->count() }} danh mục con">
                                                +{{ $category->children->count() }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox"
                                               data-category="{{ $category->id }}"
                                               {{ $category->status === 'active' ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted">
                                        {{ $category->created_at->format('d/m/Y') }}
                                        <br>
                                        <small>{{ $category->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="btn btn-outline-warning btn-action" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($category->children->count() === 0 && $category->products_count === 0)
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-action"
                                                    title="Xóa"
                                                    onclick="deleteCategory({{ $category->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-action"
                                                    title="Không thể xóa do có danh mục con hoặc sản phẩm"
                                                    disabled>
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }}
                        trong tổng số {{ $categories->total() }} danh mục
                    </div>
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có danh mục nào</h5>
                <p class="text-muted">Bạn chưa tạo danh mục nào. Hãy tạo danh mục đầu tiên!</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Tạo danh mục đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục này không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này không thể hoàn tác!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.category-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Delete category function
    function deleteCategory(categoryId) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const form = document.getElementById('deleteForm');
        form.action = `/admin/categories/${categoryId}`;
        modal.show();
    }

    // Bulk actions
    function bulkActions() {
        const checked = document.querySelectorAll('.category-checkbox:checked');
        if (checked.length === 0) {
            alert('Vui lòng chọn ít nhất một danh mục');
            return;
        }

        document.getElementById('selectedCount').textContent = checked.length;
        const modal = new bootstrap.Modal(document.getElementById('bulkModal'));
        modal.show();
    }

    function executeBulkAction(action) {
        const checked = document.querySelectorAll('.category-checkbox:checked');
        const categoryIds = Array.from(checked).map(cb => cb.value);

        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkCategories').value = JSON.stringify(categoryIds);
        document.getElementById('bulkForm').submit();
    }

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const categoryId = this.dataset.category;
            const isActive = this.checked;

            fetch(`/admin/categories/${categoryId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                     this.checked = !isActive; // Revert if failed
                    alert('Có lỗi xảy ra khi cập nhật trạng thái');
                } 
            })
            .catch(error => {
                this.checked = !isActive; // Revert if failed
                console.error('Error:', error);
            });
        });
    });

    // Featured toggle
    document.querySelectorAll('.featured-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const categoryId = this.dataset.category;
            const isFeatured = this.checked;

            fetch(`/admin/categories/${categoryId}/toggle-featured`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    this.checked = !isFeatured; // Revert if failed
                    alert('Có lỗi xảy ra khi cập nhật trạng thái nổi bật');
                }
            })
            .catch(error => {
                this.checked = !isFeatured; // Revert if failed
                console.error('Error:', error);
            });
        });
    });

    // Auto-submit search form on select change
    document.querySelectorAll('select[name="parent_id"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush
