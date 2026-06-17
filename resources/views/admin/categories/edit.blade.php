{{-- filepath: resources/views/admin/categories/edit.blade.php --}}
@extends('admin.layout')

@section('title', 'Chỉnh sửa danh mục')
@section('page-title', 'Chỉnh sửa danh mục')

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
    .image-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    #preview-img {
    width: 200px !important;
}
    .image-upload-area:hover {
        border-color: #212529;
        background: #e9ecef;
    }
    .image-upload-area.dragover {
        border-color: #212529;
        background: #e9ecef;
    }
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }
    .current-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .parent-category-tree {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .parent-category-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s;
    }
    .parent-category-item:hover {
        background-color: #e9ecef;
    }
    .parent-category-item.selected {
        background-color: #212529;
        color: white;
    }
    .parent-category-item.disabled {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }
    .info-card {
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
        <h4 class="mb-0">Chỉnh sửa danh mục</h4>
        <p class="text-muted mb-0">{{ $category->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<!-- Category Info Card -->
<div class="info-card">
    <div class="row">
        <div class="col-md-3">
            <strong>ID:</strong> #{{ $category->id }}
        </div>
        <div class="col-md-3">
            <strong>Slug:</strong> {{ $category->slug }}
        </div>
        <div class="col-md-3">
            <strong>Sản phẩm:</strong> {{ $category->products_count ?? 0 }}
        </div>
        <div class="col-md-3">
            <strong>Tạo lúc:</strong> {{ $category->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
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
                                   value="{{ old('name', $category->name) }}" placeholder="VD: Nước hoa nam">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Slug hiện tại: <code>{{ $category->slug }}</code> (sẽ được cập nhật nếu thay đổi tên)</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Mô tả về danh mục này...">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Danh mục cha</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">Không có (Danh mục gốc)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                        @if($parent->children->count() > 0)
                                            ({{ $parent->children->count() }} danh mục con)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                @if($category->parent)
                                    Hiện tại: <strong>{{ $category->parent->name }}</strong>
                                @else
                                    Hiện tại: <strong>Danh mục gốc</strong>
                                @endif
                            </small>
                        </div>

                        @if($parentCategories->count() > 0)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cây danh mục hiện tại</label>
                                <div class="parent-category-tree">
                                    @foreach($parentCategories as $parent)
                                        @php
                                            $isDisabled = false;
                                            $disabledReason = '';

                                            // Check if this parent is a descendant of current category
                                            $descendants = collect();
                                            $checkCategory = $category;
                                            while ($checkCategory && $checkCategory->children) {
                                                $children = $checkCategory->children;
                                                $descendants = $descendants->merge($children);
                                                foreach ($children as $child) {
                                                    $descendants = $descendants->merge($child->children ?? collect());
                                                }
                                                break;
                                            }

                                            if ($descendants->contains('id', $parent->id)) {
                                                $isDisabled = true;
                                                $disabledReason = 'Không thể chọn danh mục con làm cha';
                                            }
                                        @endphp

                                        <div class="parent-category-item {{ $isDisabled ? 'disabled' : '' }}"
                                             @if(!$isDisabled) onclick="selectParent({{ $parent->id }}, '{{ $parent->name }}')" @endif>
                                            <i class="fas fa-folder me-2"></i>
                                            <strong>{{ $parent->name }}</strong>

                                            @if($parent->id == $category->parent_id)
                                                <span class="badge bg-success ms-2">Hiện tại</span>
                                            @endif

                                            @if($parent->children->count() > 0)
                                                <span class="badge bg-secondary ms-2">{{ $parent->children->count() }} con</span>
                                            @endif

                                            @if($isDisabled)
                                                <span class="badge bg-warning ms-2" title="{{ $disabledReason }}">Không khả dụng</span>
                                            @endif

                                            <div class="mt-1">
                                                @foreach($parent->children as $child)
                                                    <small class="text-muted d-block ms-3">
                                                        <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i>{{ $child->name }}
                                                        @if($child->id == $category->id)
                                                            <span class="badge bg-primary ms-1">Đang chỉnh sửa</span>
                                                        @endif
                                                    </small>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Không thể chọn chính danh mục này hoặc danh mục con làm danh mục cha
                                </small>
                            </div>
                        @endif

                        @if($category->children->count() > 0)
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Danh mục con hiện tại:</h6>
                                    <div class="row">
                                        @foreach($category->children as $child)
                                            <div class="col-md-6 mb-2">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                <a href="{{ route('admin.categories.edit', $child) }}" class="text-decoration-none">
                                                    {{ $child->name }}
                                                </a>
                                                <span class="badge bg-secondary ms-1">{{ $child->products_count ?? 0 }} SP</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">
                                        Nếu thay đổi danh mục cha, các danh mục con này vẫn giữ nguyên cấu trúc.
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Category Image -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Hình ảnh danh mục</h5>
                </div>
                <div class="p-4">
                    @if($category->image)
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện tại:</label>
                            <div class="text-center">
                                <img width="200" src="{{ asset('storage/categories/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="current-image mb-2">
                                <div>
                                    <small class="text-muted">{{ $category->image }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="image-upload-area" onclick="document.getElementById('image').click()">
                        <div id="image-placeholder">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <h6>{{ $category->image ? 'Thay đổi hình ảnh' : 'Tải lên hình ảnh' }}</h6>
                            <p class="text-muted mb-0 small">Kéo thả hoặc click để chọn file</p>
                            <small class="text-muted">JPG, PNG, GIF (Max: 2MB)</small>
                        </div>
                        <div id="image-preview" class="d-none">
                            <img id="preview-img" src="" alt="Preview" class="image-preview mb-3">
                            <p class="mb-0"><i class="fas fa-edit me-2"></i>Click để thay đổi</p>
                        </div>
                    </div>
                    <input type="file" id="image" name="image" class="d-none"
                           accept="image/*" onchange="previewImage(this)">
                    @error('image')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Cài đặt</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label required-field">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>
                                Hoạt động
                            </option>
                            <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>
                                Tạm dừng
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Hiện tại:
                            <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $category->status === 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                            </span>
                        </small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input"
                                   id="is_featured" value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Danh mục nổi bật
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @if($category->products_count > 0)
                <!-- Recent Products -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Sản phẩm gần đây</h5>
                    </div>
                    <div class="p-3">
                        @forelse($category->products()->latest()->take(5)->get() as $product)
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                <img src="{{ $product->image ? asset('storage/products/' . $product->image) : asset('images/product-default.jpg') }}"
                                     alt="{{ $product->name }}"
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <div class="ms-2 flex-grow-1">
                                    <div class="fw-bold small">{{ Str::limit($product->name, 25) }}</div>
                                    <small class="text-muted">{{ number_format($product->price, 0) }}đ</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center mb-0">Chưa có sản phẩm nào</p>
                        @endforelse

                        @if($category->products_count > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">và {{ $category->products_count - 5 }} sản phẩm khác...</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <div class="d-flex gap-2">
                    @if($category->children->count() === 0 && $category->products_count === 0)
                        <button type="button" class="btn btn-outline-danger" onclick="deleteCategory()">
                            <i class="fas fa-trash me-2"></i>Xóa danh mục
                        </button>
                    @endif
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save me-2"></i>Cập nhật danh mục
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Confirmation Modal -->
@if($category->children->count() === 0 && $category->products_count === 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa danh mục</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục <strong>"{{ $category->name }}"</strong> không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này không thể hoàn tác!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa danh mục</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('image-placeholder').classList.add('d-none');
                document.getElementById('image-preview').classList.remove('d-none');
             document.getElementById('preview-img').src = e.target.result;
document.getElementById('preview-img').style.width = "200px";
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function selectParent(parentId, parentName) {
        // Remove previous selections
        document.querySelectorAll('.parent-category-item:not(.disabled)').forEach(item => {
            item.classList.remove('selected');
        });

        // Select current item
        event.currentTarget.classList.add('selected');

        // Update select dropdown
        document.querySelector('select[name="parent_id"]').value = parentId;
    }

    function deleteCategory() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Drag and drop functionality
    const uploadArea = document.querySelector('.image-upload-area');

    if (uploadArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.classList.add('dragover');
        }

        function unhighlight(e) {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            document.getElementById('image').files = files;
            previewImage(document.getElementById('image'));
        }
    }

    // Auto-select current parent in tree view
    document.addEventListener('DOMContentLoaded', function() {
        const currentParentId = {{ $category->parent_id ?? 'null' }};
        if (currentParentId) {
            const parentItem = document.querySelector(`[onclick*="${currentParentId}"]`);
            if (parentItem && !parentItem.classList.contains('disabled')) {
                parentItem.classList.add('selected');
            }
        }
    });

    // Warn about changes if category has products or children
    @if($category->children->count() > 0 || $category->products_count > 0)
    document.querySelector('form').addEventListener('submit', function(e) {
        const parentSelect = document.querySelector('select[name="parent_id"]');
        const originalParentId = {{ $category->parent_id ?? 'null' }};
        const newParentId = parentSelect.value || null;

        if (originalParentId != newParentId) {
            const confirmMessage = 'Danh mục này có {{ $category->children->count() }} danh mục con và {{ $category->products_count }} sản phẩm. Bạn có chắc chắn muốn thay đổi danh mục cha không?';

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        }
    });
    @endif
</script>
@endpush
