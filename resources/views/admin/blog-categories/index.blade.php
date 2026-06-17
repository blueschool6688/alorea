{{-- filepath: resources/views/admin/blog-categories/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Blog Categories Management')
@section('page-title', 'Quản lý danh mục Blog')

@push('styles')
<style>
    .category-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }
    .table-actions {
        white-space: nowrap;
    }
    .table-actions .btn {
        padding: 0.25rem 0.5rem;
        margin: 0 0.125rem;
    }
    .filters-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quản lý danh mục Blog</h4>
        <p class="text-muted mb-0">Danh sách tất cả danh mục blog</p>
    </div>
    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-2"></i>Tạo danh mục
    </a>
</div>
<!-- Filters -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.blog-categories.index') }}">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Tìm kiếm danh mục..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-select">
                    <option value="sort_order" {{ request('sort_by') === 'sort_order' ? 'selected' : '' }}>Thứ tự</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Tên</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                    <option value="posts_count" {{ request('sort_by') === 'posts_count' ? 'selected' : '' }}>Số bài viết</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort_order" class="form-select">
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Thông tin</th>
                            <th>Thứ tự</th>
                            <th>Số bài viết</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    @if($category->image_url)
                                        <img width="200" height="200" src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image">
                                    @else
                                        <div class="category-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ Str::limit($category->description, 80) }}</small>
                                        @endif
                                        <br><code class="small">{{ $category->slug }}</code>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->sort_order }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $category->posts_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $category->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td class="table-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blog-categories.edit', $category) }}"
                                           class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.blog-categories.toggle-status', $category) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                    title="{{ $category->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                <i class="fas fa-{{ $category->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCategory({{ $category->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị {{ $categories->firstItem() }} - {{ $categories->lastItem() }}
                    trong tổng số {{ $categories->total() }} kết quả
                </div>
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy danh mục nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo danh mục mới!</p>
                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Tạo danh mục đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
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
                    Chỉ có thể xóa danh mục không có bài viết nào!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa danh mục</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCategory(categoryId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/blog-categories/${categoryId}`;
    modal.show();
}
</script>
@endpush
