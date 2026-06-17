{{-- filepath: resources/views/admin/blogs/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Blogs Management')
@section('page-title', 'Quản lý Blog')

@push('styles')
<style>
    .blog-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
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
    .blog-excerpt {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .featured-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        font-size: 0.7rem;
        z-index: 1;
    }
    .blog-thumbnail {
        position: relative;
        display: inline-block;
    }
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .status-draft { background-color: #6c757d; }
    .status-published { background-color: #28a745; }
    .status-archived { background-color: #ffc107; color: #000; }
    .category-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .views-badge {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    .reading-time {
        font-size: 0.7rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Danh sách tất cả bài viết blog trong hệ thống</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-dark">
            <i class="fas fa-plus me-2"></i>Viết bài mới
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.blogs.index') }}" id="filterForm">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Tìm kiếm bài viết..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                        <i class="fas fa-edit"></i> Nháp
                    </option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                        <i class="fas fa-check"></i> Đã xuất bản
                    </option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>
                        <i class="fas fa-archive"></i> Lưu trữ
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="featured" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Nổi bật</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Thường</option>
                </select>
            </div>
            <div class="col-md-1">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary" title="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Actions -->
@if(request()->has('search') || request()->has('status') || request()->has('category') || request()->has('featured'))
<div class="alert alert-info d-flex align-items-center justify-content-between">
    <div>
        <i class="fas fa-filter me-2"></i>
        <strong>Đang lọc:</strong>
        @if(request('search'))
            <span class="badge bg-primary me-1">Từ khóa: "{{ request('search') }}"</span>
        @endif
        @if(request('status'))
            <span class="badge bg-secondary me-1">Trạng thái: {{ ucfirst(request('status')) }}</span>
        @endif
        @if(request('category'))
            <span class="badge bg-info me-1">Danh mục: {{ $categories->find(request('category'))->name ?? 'N/A' }}</span>
        @endif
        @if(request('featured') !== null)
            <span class="badge bg-warning me-1">{{ request('featured') == '1' ? 'Nổi bật' : 'Thường' }}</span>
        @endif
    </div>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-times me-1"></i>Xóa bộ lọc
    </a>
</div>
@endif

<!-- Blogs Table -->
<div class="card shadow-sm">
  <div class="card-body p-0">
        @if($blogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Ảnh</th>
                            <th>Thông tin bài viết</th>
                            <th width="100">Tác giả</th>
                            <th width="120">Trạng thái</th>
                            <th width="80">Thống kê</th>
                            <th width="100">Ngày tạo</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td>
                                    <div class="blog-thumbnail">
                                        @if($blog->featured_image_url)
                                            <img width="200" height="200" src="{{ $blog->featured_image_url }}" alt="{{ $blog->title }}" class="blog-image">
                                        @else
                                            <div class="blog-image bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif

                                        @if($blog->is_featured)
                                            <span class="badge bg-warning featured-badge" title="Bài viết nổi bật">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">
                                             {{ Str::limit($blog->title, 60) }}
                                        </h6>

                                        @if($blog->excerpt)
                                            <p class="text-muted small mb-1 blog-excerpt">{{ Str::limit($blog->excerpt, 100) }}</p>
                                        @endif

                                        <div class="d-flex align-items-center gap-2">
                                            <code class="small">{{ $blog->slug }}</code>

                                            @if($blog->reading_time)
                                                <span class="badge bg-light text-dark reading-time">
                                                    <i class="fas fa-clock me-1"></i>{{ $blog->reading_time }} phút
                                                </span>
                                            @endif

                                            @if($blog->allow_comments)
                                                <span class="badge bg-info" title="Cho phép bình luận">
                                                    <i class="fas fa-comments"></i>
                                                </span>
                                            @endif

                                            @if($blog->tags && count($blog->tags) > 0)
                                                <span class="badge bg-secondary" title="Tags: {{ implode(', ', $blog->tags) }}">
                                                    <i class="fas fa-tags"></i> {{ count($blog->tags) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 32px; height: 32px; font-size: 0.8rem; color: white;">
                                            {{ strtoupper(substr($blog->author_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold small">{{ $blog->author_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">

                                            @switch($blog->status)
                                                @case('draft')
                                                    <i class="fas fa-edit me-1"></i>Nháp
                                                    @break
                                                @case('published')
                                                    <i class="fas fa-check me-1"></i>Đã xuất bản
                                                    @break
                                                @case('archived')
                                                    <i class="fas fa-archive me-1"></i>Lưu trữ
                                                    @break
                                            @endswitch
                                        </span>

                                        @if($blog->published_at && $blog->status === 'published')
                                            <small class="text-muted">{{ $blog->published_at->format('d/m/Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span title="Lượt xem">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($blog->views_count ?? 0) }}
                                        </span>

                                        @if(($blog->likes_count ?? 0) > 0)
                                            <span class="badge bg-danger" title="Lượt thích">
                                                <i class="fas fa-heart me-1"></i>{{ $blog->likes_count }}
                                            </span>
                                        @endif

                                        @if(($blog->comments_count ?? 0) > 0)
                                            <span class="badge bg-success" title="Bình luận">
                                                <i class="fas fa-comment me-1"></i>{{ $blog->comments_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <small class="d-block">{{ $blog->created_at->format('d/m/Y') }}</small>
                                        <small class="text-muted">{{ $blog->created_at->format('H:i') }}</small>
                                        <small class="text-muted d-block">{{ $blog->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td class="table-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blogs.edit', $blog) }}"
                                           class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                    data-bs-toggle="dropdown" title="Thêm">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form method="POST" action="{{ route('admin.blogs.toggle-featured', $blog) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-{{ $blog->is_featured ? 'star-half-alt' : 'star' }} me-2"></i>
                                                            {{ $blog->is_featured ? 'Bỏ nổi bật' : 'Đặt nổi bật' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteBlog({{ $blog->id }})">
                                                        <i class="fas fa-trash me-2"></i>Xóa
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $blogs->firstItem() }} - {{ $blogs->lastItem() }}
                        trong tổng số {{ number_format($blogs->total()) }} kết quả
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Hiển thị:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        {{ $blogs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                @if(request()->has('search') || request()->has('status') || request()->has('category') || request()->has('featured'))
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy bài viết nào</h5>
                    <p class="text-muted">Không có bài viết nào phù hợp với tiêu chí tìm kiếm.</p>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-undo me-2"></i>Xóa bộ lọc
                    </a>
                @else
                    <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có bài viết nào</h5>
                    <p class="text-muted">Hãy tạo bài viết đầu tiên cho blog của bạn!</p>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-dark">
                        <i class="fas fa-plus me-2"></i>Viết bài đầu tiên
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Bulk Actions (if any blogs selected) -->
<div id="bulkActions" class="card mt-3" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>Đã chọn <span id="selectedCount">0</span> bài viết</strong>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-warning" onclick="bulkAction('featured')">
                    <i class="fas fa-star me-1"></i>Đặt nổi bật
                </button>
                <button class="btn btn-sm btn-info" onclick="bulkAction('published')">
                    <i class="fas fa-check me-1"></i>Xuất bản
                </button>
                <button class="btn btn-sm btn-secondary" onclick="bulkAction('draft')">
                    <i class="fas fa-edit me-1"></i>Chuyển về nháp
                </button>
                <button class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="fas fa-trash me-1"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa bài viết này không?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác! Tất cả dữ liệu liên quan sẽ bị xóa vĩnh viễn.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Hủy
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Xóa bài viết
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delete blog function
function deleteBlog(blogId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/blogs/${blogId}`;
    modal.show();
}

// Duplicate blog function
function duplicateBlog(blogId) {
    if (confirm('Bạn có muốn tạo bản sao của bài viết này không?')) {
        // Implementation for duplicating blog
        console.log('Duplicate blog:', blogId);
    }
}

// Change per page
function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}


// Bulk actions
function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.blog-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Vui lòng chọn ít nhất một bài viết');
        return;
    }

    let message = '';
    switch(action) {
        case 'delete':
            message = `Bạn có chắc chắn muốn xóa ${ids.length} bài viết đã chọn?`;
            break;
        case 'featured':
            message = `Đặt ${ids.length} bài viết đã chọn thành nổi bật?`;
            break;
        case 'published':
            message = `Xuất bản ${ids.length} bài viết đã chọn?`;
            break;
        case 'draft':
            message = `Chuyển ${ids.length} bài viết đã chọn về trạng thái nháp?`;
            break;
    }

    if (confirm(message)) {
        // Implementation for bulk actions
        console.log('Bulk action:', action, 'IDs:', ids);
        // You can create a form and submit it or use AJAX
    }
}

// Auto-submit form on filter change
document.querySelectorAll('select[name="status"], select[name="category"], select[name="featured"], select[name="sort_by"]').forEach(select => {
    select.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

// Real-time search with debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Auto-submit search after 500ms of no typing
        // Uncomment if you want auto-search
        // document.getElementById('filterForm').submit();
    }, 500);
});
</script>
@endpush
