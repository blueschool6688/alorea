{{-- filepath: resources/views/admin/users/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create User')
@section('page-title', 'Tạo người dùng mới')

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
    .password-strength {
        margin-top: 0.5rem;
    }
    .strength-bar {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Tạo người dùng mới</h4>
        <p class="text-muted mb-0">Thêm người dùng vào hệ thống</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                                   value="{{ old('name') }}" placeholder="Nhập họ và tên">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="user@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Mật khẩu</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Tối thiểu 8 ký tự" id="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="password-strength">
                                <div class="strength-bar bg-secondary" id="strengthBar"></div>
                                <small class="text-muted" id="strengthText">Nhập mật khẩu để kiểm tra độ mạnh</small>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Nhập lại mật khẩu">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="0123 456 789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="3" placeholder="Nhập địa chỉ...">{{ old('address') }}</textarea>
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
                    <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                           accept="image/*" id="avatarInput">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, PNG, WebP (max 2MB)</small>
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
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            <option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Bị cấm</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Helper -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ghi chú</h5>
                </div>
                <div class="p-4">
                    <div class="alert alert-info">
                        <small>
                            <strong>Trạng thái:</strong><br>
                            • <strong>Hoạt động:</strong> Có thể đăng nhập bình thường<br>
                            • <strong>Tạm khóa:</strong> Tạm thời không thể đăng nhập<br>
                        </small>
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
                <button type="submit" class="btn btn-dark">
                    <i class="fas fa-save me-2"></i>Tạo người dùng
                </button>
            </div>
        </div>
    </div>
</form>
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
        preview.innerHTML = '<i class="fas fa-user fa-3x text-muted"></i>';
    }
});

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    let strength = 0;
    let text = '';
    let color = '';

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    switch (strength) {
        case 0:
        case 1:
            text = 'Rất yếu';
            color = 'bg-danger';
            break;
        case 2:
            text = 'Yếu';
            color = 'bg-warning';
            break;
        case 3:
            text = 'Trung bình';
            color = 'bg-info';
            break;
        case 4:
            text = 'Mạnh';
            color = 'bg-success';
            break;
        case 5:
            text = 'Rất mạnh';
            color = 'bg-primary';
            break;
    }

    strengthBar.className = `strength-bar ${color}`;
    strengthBar.style.width = `${(strength / 5) * 100}%`;
    strengthText.textContent = text;
});
</script>
@endpush
