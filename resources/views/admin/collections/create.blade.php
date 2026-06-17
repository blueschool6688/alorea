{{-- filepath: resources/views/admin/collections/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create Collection')
@section('page-title', 'Tạo bộ sưu tập mới')

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
    .video-preview {
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
    .product-selection {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
    }
    .product-item {
        padding: 0.5rem;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
    }
    .product-item:hover {
        background: #f8f9fa;
    }
    .product-item.selected {
        background: #e7f3ff;
        border-color: #007bff;
    }
    .slug-preview {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Tạo bộ sưu tập mới</h4>
        <p class="text-muted mb-0">Thêm bộ sưu tập sản phẩm vào hệ thống</p>
    </div>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label required-field">Tiêu đề</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="Nhập tiêu đề bộ sưu tập" id="titleInput">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="Tự động tạo từ tiêu đề" id="slugInput">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="slug-preview mt-2" id="slugPreview">URL: /collections/your-slug</div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Phụ đề</label>
                            <input required type="text" name="sub_title" class="form-control @error('sub_title') is-invalid @enderror"
                                   value="{{ old('sub_title') }}" placeholder="Phụ đề mô tả ngắn">
                            @error('sub_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea id="contentEditor" name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="5" placeholder="Mô tả chi tiết về bộ sưu tập...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Selection -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Chọn sản phẩm</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." id="productSearch">
                    </div>

                    <div class="product-selection" id="productList">
                        @foreach($products as $product)
                            <div class="product-item" data-product-id="{{ $product->id }}" data-product-name="{{ strtolower($product->name) }}">
                                <div class="form-check">
                                    <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                           class="form-check-input" id="product_{{ $product->id }}"
                                           {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="product_{{ $product->id }}">
                                        <div class="d-flex align-items-center">
                                            @if($product->main_image)
                                                <img src="{{ asset('storage/products/' . $product->main_image) }}"
                                                     alt="{{ $product->name }}" class="me-3"
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; border-radius: 4px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ number_format($product->price) }}đ</small>
                                                @if($product->category)
                                                    <small class="text-muted"> • {{ $product->category->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 text-muted">
                        <span id="selectedCount">0</span> sản phẩm được chọn
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Video Upload -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-video me-2"></i>Video bộ sưu tập</h5>
                </div>
                <div class="p-4">
                    <div class="video-preview mb-3" id="videoPreview">
                        <span>Video preview sẽ hiển thị ở đây</span>
                    </div>

                    <input type="file" name="video" class="form-control @error('video') is-invalid @enderror"
                           accept="video/*" id="videoInput">
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">MP4, AVI, MOV (max 50MB)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <button type="submit" class="btn btn-dark">
                    <i class="fas fa-save me-2"></i>Tạo bộ sưu tập
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#contentEditor',
        height: 800,
        menubar: false,
        license_key: 'gpl',

        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media link | table | code preview fullscreen | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        base_url: '{{ asset("js/tinymce") }}',
        suffix: '.min',
        images_upload_url: '{{ route("admin.tinymce.upload") }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '{{ route("admin.tinymce.upload") }}');

                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = function() {
                    if (xhr.status === 403) {
                        reject({message: 'HTTP Error: ' + xhr.status, remove: true});
                        return;
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    const json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    resolve(json.location);
                };

                xhr.onerror = function () {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            });
        },
        image_advtab: true,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        object_resizing: true,
        resize_img_proportional: true,
        table_default_attributes: {
            class: 'table table-striped table-bordered'
        },
        table_default_styles: {},
        link_assume_external_targets: true,
        target_list: [
            {title: 'New window', value: '_blank'},
            {title: 'Same window', value: '_self'}
        ],
        setup: function(editor) {
            editor.on('init', function() {
                console.log('TinyMCE Self-hosted đã khởi tạo thành công!');
            });

            editor.on('input keyup', function() {
            });
        }
    });
// Auto generate slug from title
document.getElementById('titleInput').addEventListener('input', function() {
    const title = this.value;
    const slug = title.toLowerCase()
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
    preview.textContent = `URL: /collections/${slug || 'your-slug'}`;
}

// Video preview
document.getElementById('videoInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('videoPreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<video controls style="width: 100%; max-height: 200px;"><source src="${e.target.result}" type="video/mp4"></video>`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="fas fa-video fa-2x me-2"></i><span>Video preview sẽ hiển thị ở đây</span>';
    }
});

// Product search
document.getElementById('productSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const productItems = document.querySelectorAll('.product-item');

    productItems.forEach(item => {
        const productName = item.dataset.productName;
        if (productName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Count selected products
function updateSelectedCount() {
    const checkedProducts = document.querySelectorAll('input[name="products[]"]:checked');
    document.getElementById('selectedCount').textContent = checkedProducts.length;
}

// Product selection handling
document.querySelectorAll('input[name="products[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const productItem = this.closest('.product-item');
        if (this.checked) {
            productItem.classList.add('selected');
        } else {
            productItem.classList.remove('selected');
        }
        updateSelectedCount();
    });
});

// Initialize selected count
updateSelectedCount();

// Initialize selected products styling
document.querySelectorAll('input[name="products[]"]:checked').forEach(checkbox => {
    checkbox.closest('.product-item').classList.add('selected');
});
</script>
@endpush
