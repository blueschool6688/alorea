{{-- filepath: resources/views/admin/users/edit.blade.php --}}
@extends('admin.layout')

@section('title', 'Edit User')
@section('page-title', 'Chỉnh sửa người dùng')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dee2e6;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    .avatar-preview img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .current-avatar {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Chỉnh sửa người dùng</h4>
        <p class="text-muted mb-0">{{ $user->name }} - {{ $user->email }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cơ bản</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Họ và tên</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" placeholder="Nhập họ và tên">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" placeholder="user@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Để trống nếu không đổi">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Nhập lại mật khẩu mới">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}" placeholder="0123 456 789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="3" placeholder="Nhập địa chỉ...">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Avatar -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Ảnh đại diện</h5>
                </div>
                <div class="p-4 text-center">
                    @if($user->avatar_url)
                        <div class="current-avatar">
                            <p class="mb-2"><strong>Ảnh hiện tại:</strong></p>
                            <img src="{{ $user->avatar_url }}" alt="Current Avatar"
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        </div>
                    @endif


                    <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                           accept="image/*" id="avatarInput">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        {{ $user->avatar ? 'Để trống để giữ ảnh hiện tại' : 'Chọn ảnh đại diện' }}
                        <br>JPG, PNG, WebP (max 2MB)
                    </small>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Cài đặt tài khoản</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label required-field">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="">Chọn trạng thái</option>
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Bị cấm</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Hiện tại: <strong>{{ ucfirst($user->status) }}</strong></small>
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin tài khoản</h5>
                </div>
                <div class="p-4">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <small class="text-muted">Tạo lúc</small>
                                <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <small class="text-muted">Cập nhật lần cuối</small>
                                <div class="fw-bold">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        @if($user->last_login_at)
                        <div class="col-12">
                            <div class="border rounded p-2">
                                <small class="text-muted">Đăng nhập lần cuối</small>
                                <div class="fw-bold">{{ $user->last_login_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <div class="d-flex gap-2">
                    @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-outline-danger" onclick="deleteUser()">
                            <i class="fas fa-trash me-2"></i>Xóa người dùng
                        </button>
                    @endif
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa người dùng <strong>"{{ $user->name }}"</strong> không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này không thể hoàn tác!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
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
// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('avatarPreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    } else {
        @if($user->avatar)
            preview.innerHTML = '<img src="{{ $user->avatar_url }}" alt="Avatar">';
        @else
            preview.innerHTML = '<i class="fas fa-user fa-3x text-muted"></i>';
        @endif
    }
});

function deleteUser() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
