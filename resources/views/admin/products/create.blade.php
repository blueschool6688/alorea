{{-- filepath: resources/views/admin/products/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create Product')
@section('page-title', 'Create Product')

@push('styles')
<style>
    .form-check-group {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .form-check-inline {
        margin-right: 0;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    .card-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }
    .card-header h5 {
        margin: 0;
        font-weight: 600;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .technical-specs .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .technical-specs .form-control:focus,
    .technical-specs .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .technical-specs .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    .form-check-group {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    .form-check-group .form-check {
        margin-bottom: 8px;
    }
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    .input-group-text {
        font-weight: 500;
        color: #495057;
    }
    .technical-specs textarea {
        resize: vertical;
        min-height: 80px;
    }
    .text-orange {
        color: #fd7e14 !important;
    }
    .form-check-group .row .form-check {
        margin-bottom: 10px;
    }
    .form-check-label {
        cursor: pointer;
        padding-left: 5px;
    }
    .badge {
        cursor: pointer;
        transition: all 0.2s;
    }
    .badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .technical-specs .alert {
        font-size: 0.9rem;
    }
    .technical-specs .form-label i {
        width: 16px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Create New Product</h4>
        <p class="text-muted mb-0">Add a new perfume product to your store</p>
    </div>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Enter product name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}" placeholder="Auto-generated if empty">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                                   value="{{ old('barcode') }}" placeholder="Product barcode">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description <span class="text-danger">*</span></label>
                        <textarea name="short_description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Detailed product description" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" id="short_description" class="form-control @error('short_description') is-invalid @enderror"
                                 placeholder="Brief product description">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Pricing & Inventory</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}" placeholder="0.00" step="0.01" min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock') }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>Product Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Main Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                               accept="image/*" required>
                        <small class="form-text text-muted">Upload main product image (JPG, PNG, WebP)</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" name="gallery[]" class="form-control @error('gallery') is-invalid @enderror"
                               accept="image/*" multiple>
                        <small class="form-text text-muted">Upload additional product images (multiple files allowed)</small>
                        @error('gallery')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SEO Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>SEO Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
                               value="{{ old('meta_title') }}" placeholder="SEO title">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                  rows="3" placeholder="SEO description">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
                               value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Technical Specifications -->
            <div class="card mb-4 technical-specs">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>Thông số kỹ thuật
                        <small class="text-light ms-2">(Tùy chọn - Thêm thông tin chi tiết về sản phẩm)</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nồng độ tinh dầu -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-tint me-1 text-primary"></i>Nồng độ tinh dầu
                            </label>
                            <input type="text" name="concentration" class="form-control @error('concentration') is-invalid @enderror"
                                   value="{{ old('concentration') }}" placeholder="Ví dụ: EDP (15-20%)">

                            @error('concentration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tỷ lệ tinh dầu thơm trong sản phẩm</small>
                        </div>

                        <!-- Dung tích -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-flask me-1 text-info"></i>Dung tích
                            </label>
                            <div class="input-group">
                                <input type="number" name="volume_ml" class="form-control @error('volume_ml') is-invalid @enderror"
                                       value="{{ old('volume_ml') }}" placeholder="50" min="1" max="1000">
                                <span class="input-group-text">ml</span>
                            </div>
                            @error('volume_ml')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dung tích chai nước hoa (ml)</small>
                        </div>

                        <!-- Độ lưu hương và Độ tỏa hương -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-1 text-warning"></i>Độ lưu hương
                            </label>
                            <input type="text" name="longevity" class="form-control @error('longevity') is-invalid @enderror"
                                   value="{{ old('longevity') }}" placeholder="Ví dụ: 8-10 giờ">

                            @error('longevity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Thời gian mùi hương tồn tại trên da</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-wind me-1 text-secondary"></i>Độ tỏa hương
                            </label>
                            <input type="text" name="sillage" class="form-control @error('sillage') is-invalid @enderror"
                                   value="{{ old('sillage') }}" placeholder="Ví dụ: Mạnh (Trong phạm vi 2-3m)">

                            @error('sillage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Phạm vi tỏa mùi của nước hoa</small>
                        </div>


                        <!-- Thành phần chính -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-leaf me-1 text-success"></i>Thành phần chính
                            </label>
                            <textarea name="main_ingredients" class="form-control @error('main_ingredients') is-invalid @enderror"
                                      rows="4" placeholder="Ví dụ: Bergamot, Hoa hồng, Xạ hương, Vanilla, Sandalwood..."
                                      id="ingredients-textarea">{{ old('main_ingredients') }}</textarea>
                            @error('main_ingredients')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 sticky-top">
            <div class="sticky-top" style="top: 20px;">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Publishing Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="men" {{ old('type') == 'men' ? 'selected' : '' }}>Men</option>
                                <option value="women" {{ old('type') == 'women' ? 'selected' : '' }}>Women</option>
                                <option value="unisex" {{ old('type') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mùi Hương</label>
                            <select name="scrent_id" class="form-select @error('scrent_id') is-invalid @enderror">
                                <option value="">Select Scent</option>
                                @foreach($scents as $scent)
                                    <option value="{{ $scent->id }}" {{ old('scrent_id') == $scent->id ? 'selected' : '' }}>
                                        {{ $scent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('scrent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input"
                                    id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Featured Product
                                </label>
                            </div>
                            <small class="form-text text-muted">Display in featured products section</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Category & Organization</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" name="action" value="save" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    let editor;
// Auto-generate SKU from product name
document.querySelector('input[name="name"]').addEventListener('input', function() {
    const skuField = document.querySelector('input[name="sku"]');
    if (!skuField.value) {
        const name = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);
        if (name) {
            skuField.value = 'PRD-' + name + Math.floor(Math.random() * 100);
        }
    }
});

// Show ingredients suggestions
function showIngredientsSuggestions() {
    const suggestionsDiv = document.getElementById('ingredients-suggestions');
    if (suggestionsDiv.style.display === 'none') {
        suggestionsDiv.style.display = 'block';

        // Add click handlers to badges
        const badges = suggestionsDiv.querySelectorAll('.badge');
        badges.forEach(badge => {
            badge.addEventListener('click', function() {
                const textarea = document.getElementById('ingredients-textarea');
                const ingredient = this.textContent;

                if (textarea.value === '') {
                    textarea.value = ingredient;
                } else {
                    textarea.value += ', ' + ingredient;
                }

                // Visual feedback
                this.style.backgroundColor = '#28a745';
                this.style.color = 'white';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }, 1000);
            });
        });
    } else {
        suggestionsDiv.style.display = 'none';
    }
}

// Show notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Form validation
document.getElementById('productForm').addEventListener('submit', function(e) {
    const price = parseFloat(document.querySelector('input[name="price"]').value);
    const comparePrice = parseFloat(document.querySelector('input[name="compare_price"]').value);

    if (comparePrice && comparePrice <= price) {
        e.preventDefault();
        alert('Compare price should be higher than regular price.');
        return false;
    }
});

// Preview uploaded images
document.querySelector('input[name="image"]').addEventListener('change', function(e) {
    previewImage(e.target, 'main-image-preview');
});

document.querySelector('input[name="gallery[]"]').addEventListener('change', function(e) {
    previewGallery(e.target, 'gallery-preview');
});

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById(previewId);
            if (!preview) {
                preview = document.createElement('img');
                preview.id = previewId;
                preview.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px;';
                input.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewGallery(input, previewId) {
    let preview = document.getElementById(previewId);
    if (preview) {
        preview.remove();
    }

    if (input.files && input.files.length > 0) {
        preview = document.createElement('div');
        preview.id = previewId;
        preview.style.cssText = 'display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;';

        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 80px; height: 80px; object-fit: cover; border-radius: 8px;';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });

        input.parentNode.appendChild(preview);
    }
}

// Initialize simple CKEditor for short description

class LocalImageAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = () => {
                    resolve({
                        default: reader.result
                    });
                };

                reader.onerror = () => {
                    reject('Không thể đọc file ảnh');
                };

                reader.readAsDataURL(file);
            });
        });
    }

    abort() {
        // Không cần abort cho local file
    }
}

tinymce.init({
        selector: '#short_description',
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
                updateWordCount();
            });

            editor.on('input keyup', function() {
                updateWordCount();
            });
        }
    });

    function updateWordCount() {
        const editor = tinymce.get('short_description');
        if (editor) {
            const content = editor.getContent({format: 'text'});
            const wordCount = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;

            let counter = document.getElementById('word-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'word-counter';
                counter.className = 'text-muted small mt-1';
                document.querySelector('#short_description').parentNode.appendChild(counter);
            }
            counter.textContent = `Số từ: ${wordCount}`;
        }
    }

</script>

@endpush

