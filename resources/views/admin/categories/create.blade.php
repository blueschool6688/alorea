@extends('admin.layout')

@section('title', 'Tạo danh mục mới')
@section('page-title', 'Tạo danh mục')

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
    .image-upload-area:hover {
        border-color: #212529;
        background: #e9ecef;
    }
    .image-upload-area.dragover {
        border-color: #212529;
        background: #e9ecef;
    }
    #preview-img {
        width: 200px !important;
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
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Tạo danh mục mới</h4>
        <p class="text-muted mb-0">Thêm danh mục sản phẩm nước hoa</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                                   value="{{ old('name') }}" placeholder="VD: Nước hoa nam">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Slug sẽ được tự động tạo từ tên danh mục</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Mô tả về danh mục này...">{{ old('description') }}</textarea>
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
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
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
                            <small class="text-muted">Để trống nếu đây là danh mục gốc</small>
                        </div>

                        @if($parentCategories->count() > 0)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cây danh mục hiện tại</label>
                                <div class="parent-category-tree">
                                    @foreach($parentCategories as $parent)
                                        <div class="parent-category-item" onclick="selectParent({{ $parent->id }}, '{{ $parent->name }}')">
                                            <i class="fas fa-folder me-2"></i>
                                            <strong>{{ $parent->name }}</strong>
                                            @if($parent->children->count() > 0)
                                                <span class="badge bg-secondary ms-2">{{ $parent->children->count() }} con</span>
                                            @endif
                                            <div class="mt-1">
                                                @foreach($parent->children as $child)
                                                    <small class="text-muted d-block ms-3">
                                                        <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i>{{ $child->name }}
                                                    </small>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- <!-- SEO Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cài đặt SEO</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title"
                                   class="form-control @error('meta_title') is-invalid @enderror"
                                   value="{{ old('meta_title') }}"
                                   placeholder="Tiêu đề SEO (để trống để sử dụng tên danh mục)">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tối đa 60 ký tự để hiển thị tốt trên Google</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" rows="3"
                                      class="form-control @error('meta_description') is-invalid @enderror"
                                      placeholder="Mô tả SEO...">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tối đa 160 ký tự để hiển thị đầy đủ trên Google</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords"
                                   class="form-control @error('meta_keywords') is-invalid @enderror"
                                   value="{{ old('meta_keywords') }}"
                                   placeholder="từ khóa, nước hoa, danh mục">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Các từ khóa cách nhau bằng dấu phẩy</small>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="col-lg-4">
            <!-- Category Image -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Hình ảnh danh mục</h5>
                </div>
                <div class="p-4">
                    <div class="image-upload-area" onclick="document.getElementById('image').click()">
                        <div id="image-placeholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h6>Tải lên hình ảnh</h6>
                            <p class="text-muted mb-0">Kéo thả hoặc click để chọn file</p>
                            <small class="text-muted">JPG, PNG, GIF (Max: 2MB)</small>
                        </div>
                        <div id="image-preview" class="d-none">
                            <img height="200" id="preview-img" src="" alt="Preview" style="width: 200px !important;" class="image-preview mb-3">
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
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                <i class="fas fa-check-circle me-2"></i>Hoạt động
                            </option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                <i class="fas fa-pause-circle me-2"></i>Tạm dừng
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input"
                                   id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Is Featured
                            </label>
                        </div>
                    </div>
            </div>
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
                    <button type="submit" name="action" value="save" class="btn btn-dark">
                        <i class="fas fa-check me-2"></i>Tạo danh mục
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
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
                document.getElementById('preview-category-image').src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function selectParent(parentId, parentName) {
        // Remove previous selections
        document.querySelectorAll('.parent-category-item').forEach(item => {
            item.classList.remove('selected');
        });

        // Select current item
        event.currentTarget.classList.add('selected');

        // Update select dropdown
        document.querySelector('select[name="parent_id"]').value = parentId;
    }

    // Real-time preview updates
    document.querySelector('input[name="name"]').addEventListener('input', function() {
        const previewName = document.getElementById('preview-category-name');
        previewName.textContent = this.value || 'Tên danh mục';
    });

    document.querySelector('textarea[name="description"]').addEventListener('input', function() {
        const previewDesc = document.getElementById('preview-category-desc');
        previewDesc.textContent = this.value || 'Mô tả danh mục sẽ hiển thị ở đây...';
    });

    // Drag and drop functionality
    const uploadArea = document.querySelector('.image-upload-area');

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

    // Character counter for meta fields
    function addCharacterCounter(inputSelector, maxLength) {
        const input = document.querySelector(inputSelector);
        if (input) {
            const counter = document.createElement('small');
            counter.className = 'form-text text-muted';
            input.parentNode.appendChild(counter);

            function updateCounter() {
                const length = input.value.length;
                counter.textContent = `${length}/${maxLength} ký tự`;
                counter.className = length > maxLength ? 'form-text text-danger' : 'form-text text-muted';
            }

            input.addEventListener('input', updateCounter);
            updateCounter();
        }
    }

    addCharacterCounter('input[name="meta_title"]', 60);
    addCharacterCounter('textarea[name="meta_description"]', 160);
</script>
@endpush
