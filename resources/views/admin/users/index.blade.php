{{-- filepath: resources/views/admin/users/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Users Management')
@section('page-title', 'Quản lý người dùng')

@push('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #dee2e6;
    }
    .table-actions {
        white-space: nowrap;
    }
    .table-actions .btn {
        padding: 0.25rem 0.5rem;
        margin: 0 0.125rem;
    }
    .status-active { color: #28a745; }
    .status-inactive { color: #ffc107; }
    .status-banned { color: #dc3545; }
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
        <h4 class="mb-0">Quản lý người dùng</h4>
        <p class="text-muted mb-0">Danh sách tất cả người dùng trong hệ thống</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-2"></i>Thêm người dùng
    </a>
</div>

<!-- Filters -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control"
                       placeholder="Tìm kiếm..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Tên</option>
                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="last_login_at" {{ request('sort_by') === 'last_login_at' ? 'selected' : '' }}>Đăng nhập cuối</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Thông tin</th>
                            <th>Liên hệ</th>
                            <th>Trạng thái</th>
                            <th>Đăng nhập cuối</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="">
                                    @if ($user->avatar_url)
                                        <img width="100" height="100" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar">
                                    @else
                                        <strong class="text-muted">Trống</strong>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        @if($user->date_of_birth)
                                            <br>
                                            <small class="text-muted">{{ $user->date_of_birth->format('d/m/Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($user->phone)
                                        <div><i class="fas fa-phone text-muted me-1"></i>{{ $user->phone }}</div>
                                    @endif
                                    @if($user->address)
                                        <div><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ Str::limit($user->address, 30) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $user->status_badge }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        <small class="text-muted">Chưa đăng nhập</small>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="table-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteUser({{ $user->id }})" title="Xóa"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
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
                    Hiển thị {{ $users->firstItem() }} - {{ $users->lastItem() }}
                    trong tổng số {{ $users->total() }} kết quả
                </div>
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy người dùng nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo người dùng mới!</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Thêm người dùng đầu tiên
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
                <p>Bạn có chắc chắn muốn xóa người dùng này không?</p>
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
                    <button type="submit" class="btn btn-danger">Xóa người dùng</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteUser(userId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/users/${userId}`;
    modal.show();
}
</script>
@endpush
