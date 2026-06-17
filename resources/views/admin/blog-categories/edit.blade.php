{{-- filepath: resources/views/admin/blog-categories/edit.blade.php --}}
@extends('admin.layout')

@section('title', 'Edit Blog Category')
@section('page-title', 'Chỉnh sửa danh mục Blog')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .image-preview {
        width: 100%;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .current-image {
        background: #e8f4ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .slug-preview {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
    }
    .category-info {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 1rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Chỉnh sửa danh mục Blog</h4>
        <p class="text-muted mb-0">{{ $blogCategory->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog-categories.show', $blogCategory) }}" class="btn btn-outline-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-dark">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<form action="{{ route('admin.blog-categories.update', $blogCategory) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label required-field">Tên danh mục</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $blogCategory->name) }}" placeholder="Ví dụ: Tin tức" id="nameInput">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $blogCategory->slug) }}" placeholder="Tự động tạo từ tên" id="slugInput">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Mô tả chi tiết về danh mục...">{{ old('description', $blogCategory->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cài đặt SEO</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
                                   value="{{ old('meta_title', $blogCategory->meta_title) }}" placeholder="Tiêu đề SEO (để trống để sử dụng tên danh mục)">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Độ dài khuyến nghị: 50-60 ký tự</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                      rows="3" placeholder="Mô tả SEO cho danh mục...">{{ old('meta_description', $blogCategory->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Độ dài khuyến nghị: 150-160 ký tự</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Image Upload -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Hình ảnh danh mục</h5>
                </div>
                <div class="p-4">


                    <div class="image-preview" id="imagePreview">
                        @if($blogCategory->image)
                            <img src="{{ $blogCategory->image_url }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">
                        @else
                            <i class="fas fa-image fa-2x me-2"></i>
                            <span>Hình ảnh sẽ hiển thị ở đây</span>
                        @endif
                    </div>

                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                           accept="image/*" id="imageInput">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        {{ $blogCategory->image ? 'Để trống để giữ hình ảnh hiện tại' : 'Chọn hình ảnh' }}
                        <br>JPG, PNG, WEBP (max 5MB)
                    </small>
                </div>
            </div>

            <!-- Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Cài đặt</h5>
                </div>
                <div class="p-4">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input"
                               id="isActive" {{ old('is_active', $blogCategory->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Kích hoạt danh mục
                        </label>
                    </div>
                </div>
            </div>

            <!-- Category Info -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin danh mục</h5>
                </div>
                <div class="p-4">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <small class="text-muted">Tạo lúc</small>
                                <div class="fw-bold">{{ $blogCategory->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <small class="text-muted">Cập nhật lần cuối</small>
                                <div class="fw-bold">{{ $blogCategory->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-2">
                                <small class="text-muted">Số bài viết</small>
                                <div class="fw-bold">{{ $blogCategory->posts_count ?? 0 }} bài viết</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteCategory()">
                        <i class="fas fa-trash me-2"></i>Xóa danh mục
                    </button>
                    <button type="submit" class="btn btn-primary">
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
                <p>Bạn có chắc chắn muốn xóa danh mục <strong>"{{ $blogCategory->name }}"</strong> không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này sẽ xóa danh mục và tất cả dữ liệu liên quan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.blog-categories.destroy', $blogCategory) }}" method="POST" style="display: inline;">
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
// Auto generate slug from name
document.getElementById('nameInput').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
                     .replace(/[^a-z0-9\s-]/g, '')
                     .replace(/\s+/g, '-')
                     .replace(/-+/g, '-')
                     .trim();

    document.getElementById('slugInput').value = slug;
    updateSlugPreview(slug);
});

document.getElementById('slugInput').addEventListener('input', function() {
    updateSlugPreview(this.value);
});

function updateSlugPreview(slug) {
    const preview = document.getElementById('slugPreview');
    preview.textContent = `URL: /blog/categories/${slug || 'your-slug'}`;
}

// Image preview
document.getElementById('imageInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        @if($blogCategory->image)
            preview.innerHTML = '<img src="{{ $blogCategory->image_url }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">';
        @else
            preview.innerHTML = '<i class="fas fa-image fa-2x me-2"></i><span>Hình ảnh sẽ hiển thị ở đây</span>';
        @endif
    }
});

// Color picker
document.getElementById('colorInput').addEventListener('input', function() {
    const color = this.value;
    document.getElementById('colorPreview').style.backgroundColor = color;
});

function deleteCategory() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Initialize slug preview
updateSlugPreview(document.getElementById('slugInput').value);
</script>
@endpush
