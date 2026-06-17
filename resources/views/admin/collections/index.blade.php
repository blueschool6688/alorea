{{-- filepath: resources/views/admin/collections/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Collections Management')
@section('page-title', 'Quản lý bộ sưu tập')

@push('styles')
<style>
    .collection-video {
        width: 60px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
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
    .collection-stats {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quản lý bộ sưu tập</h4>
        <p class="text-muted mb-0">Danh sách tất cả bộ sưu tập sản phẩm</p>
    </div>
    <a href="{{ route('admin.collections.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-2"></i>Tạo bộ sưu tập
    </a>
</div>
<!-- Filters -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.collections.index') }}">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Tìm kiếm bộ sưu tập..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                    <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Tên</option>
                    <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Ngày cập nhật</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Collections Table -->
<div class="card">
    <div class="card-body">
        @if($collections->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Video</th>
                            <th>Thông tin</th>
                            <th>Slug</th>
                            <th>Sản phẩm</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collections as $collection)
                            <tr>
                                <td>
                                    @if($collection->video_url)
                                        <video width="200" height="200" class="collection-video" muted controls>
                                            <source src="{{ $collection->video_url }}" type="video/mp4">
                                        </video>
                                    @else
                                        <div class="collection-video bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-video text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $collection->title }}</strong>
                                        @if($collection->sub_title)
                                            <br><small class="text-muted">{{ $collection->sub_title }}</small>
                                        @endif
                                        @if($collection->description)
                                            <br><small class="text-muted">{{ Str::limit($collection->description, 60) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $collection->slug }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $collection->products_count ?? $collection->products->count() }} sản phẩm
                                    </span>
                                </td>
                                <td>{{ $collection->created_at->format('d/m/Y') }}</td>
                                <td class="table-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.collections.edit', $collection) }}"
                                           class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCollection({{ $collection->id }})" title="Xóa">
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
                <div class="text-muted collection-stats">
                    Hiển thị {{ $collections->firstItem() }} - {{ $collections->lastItem() }}
                    trong tổng số {{ $collections->total() }} kết quả
                </div>
                {{ $collections->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy bộ sưu tập nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo bộ sưu tập mới!</p>
                <a href="{{ route('admin.collections.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Tạo bộ sưu tập đầu tiên
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
                <p>Bạn có chắc chắn muốn xóa bộ sưu tập này không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này sẽ xóa bộ sưu tập và tất cả liên kết với sản phẩm!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa bộ sưu tập</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCollection(collectionId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/collections/${collectionId}`;
    modal.show();
}

// Play video on hover
document.querySelectorAll('.collection-video').forEach(video => {
    if (video.tagName === 'VIDEO') {
        video.addEventListener('mouseenter', () => video.play());
        video.addEventListener('mouseleave', () => video.pause());
    }
});
</script>
@endpush
