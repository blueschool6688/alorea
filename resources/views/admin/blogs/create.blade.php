{{-- filepath: resources/views/admin/blogs/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create Blog Post')
@section('page-title', 'Viết bài blog mới')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    .slug-preview {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    .tags-input {
        min-height: 45px;
        cursor: text;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: white;
    }
    .tag-item {
        display: inline-block;
        background: #007bff;
        color: white;
        border-radius: 15px;
        padding: 2px 8px;
        margin: 2px;
        font-size: 0.85rem;
    }
    .tag-remove {
        margin-left: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    .gallery-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .gallery-item {
        position: relative;
        width: 80px;
        height: 80px;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .gallery-item .remove-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        cursor: pointer;
    }
    /* CKEditor custom styles */
    .ck-editor__editable_inline {
        min-height: 400px;
    }
    .word-count {
        text-align: right;
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .reading-time-display {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Viết bài blog mới</h4>
        <p class="text-muted mb-0">Tạo bài viết blog cho website</p>
    </div>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Thông tin bài viết</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label required-field">Tiêu đề bài viết</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="Nhập tiêu đề hấp dẫn..." id="titleInput">
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
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tóm tắt bài viết</label>
                            <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror"
                                      rows="3" placeholder="Tóm tắt ngắn gọn về nội dung bài viết..."
                                      id="excerptInput" maxlength="500">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Tóm tắt giúp độc giả hiểu nhanh nội dung</small>
                                <small class="text-muted"><span id="excerptCount">0</span>/500</small>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label required-field">Nội dung bài viết</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                      id="contentEditor">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="word-count" id="wordCount">0 từ</div>
                            <div class="reading-time-display" id="readingTime">
                                <i class="fas fa-clock me-1"></i>Thời gian đọc: <span id="readingMinutes">0</span> phút
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label required-field">Tác giả</label>
                            <input type="text" name="author_name" class="form-control @error('author_name') is-invalid @enderror"
                                   value="{{ old('author_name', auth()->user()->name ?? '') }}" placeholder="Tên tác giả">
                            @error('author_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="col-md-12 mb-3">
                            <label class="form-label">Tags</label>
                            <div class="tags-input" id="tagsInput" onclick="focusTagInput()">
                                <input type="text" id="tagInputField" placeholder="Nhập tag và nhấn Enter..."
                                       style="border: none; outline: none; background: transparent; min-width: 200px;">
                            </div>
                            <input type="hidden" name="tags" id="tagsHidden">
                            <small class="text-muted">Nhấn Enter để thêm tag, click vào tag để xóa</small>
                        </div> --}}

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nguồn bài viết (tùy chọn)</label>
                            <input type="url" name="source_url" class="form-control @error('source_url') is-invalid @enderror"
                                   value="{{ old('source_url') }}" placeholder="https://example.com">
                            @error('source_url')
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
                                   value="{{ old('meta_title') }}" placeholder="Để trống để sử dụng tiêu đề bài viết"
                                   maxlength="60" id="metaTitleInput">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Tiêu đề hiển thị trên search engine</small>
                                <small class="text-muted"><span id="metaTitleCount">0</span>/60</small>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                      rows="3" placeholder="Mô tả bài viết cho search engine..."
                                      maxlength="160" id="metaDescInput">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Mô tả hiển thị trên search engine</small>
                                <small class="text-muted"><span id="metaDescCount">0</span>/160</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Publish Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Cài đặt xuất bản</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label required-field">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" id="statusSelect">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Xuất bản ngay</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_featured" class="form-check-input"
                               id="isFeatured" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="isFeatured">
                            <i class="fas fa-star text-warning me-1"></i>
                            Bài viết nổi bật
                        </label>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Ảnh đại diện</h5>
                </div>
                <div class="p-4">
                    <div class="image-preview" id="featuredImagePreview">
                        <i class="fas fa-image fa-2x me-2"></i>
                        <span>Ảnh đại diện bài viết</span>
                    </div>

                    <input type="file" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror"
                           accept="image/*" id="featuredImageInput">
                    @error('featured_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, PNG, WEBP (max 5MB)</small>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" name="save_as" value="published" class="btn btn-success sticky-bottom my-3" style="float: right; bottom: 10px;">
        <i class="fas fa-paper-plane me-2"></i>Xuất bản
    </button>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
let tags = [];

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
                updateWordCount();
                updateReadingTime();
            });

            editor.on('input keyup', function() {
                updateWordCount();
                updateReadingTime();
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
    preview.textContent = `URL: /blog/${slug || 'your-slug'}`;
}

// Character counters
document.getElementById('excerptInput').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('excerptCount').textContent = count;
});

document.getElementById('metaTitleInput').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('metaTitleCount').textContent = count;
});

document.getElementById('metaDescInput').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('metaDescCount').textContent = count;
});

 function updateWordCount() {
        const editor = tinymce.get('contentEditor');
        if (editor) {
            const content = editor.getContent({format: 'text'});
            const wordCount = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
            let counter = document.getElementById('word-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'word-counter';
                counter.className = 'text-muted small mt-1';
                document.querySelector('#contentEditor').parentNode.appendChild(counter);
            }
            console.log(wordCount,editor);
            counter.textContent = `Số từ: ${wordCount}`;
        }
    }
function updateReadingTime() {
    const wordCount = updateWordCount();
    const readingTime = Math.max(1, Math.ceil(wordCount / 200)); // 200 words per minute
    document.getElementById('readingMinutes').textContent = readingTime;
}

// Featured image preview
document.getElementById('featuredImageInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('featuredImagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="fas fa-image fa-2x me-2"></i><span>Ảnh đại diện bài viết</span>';
    }
});

// Gallery preview
document.getElementById('galleryInput').addEventListener('change', function() {
    const files = this.files;
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();

        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'gallery-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Gallery image">
                <button type="button" class="remove-btn" onclick="removeGalleryItem(this)">×</button>
            `;
            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    }
});

function removeGalleryItem(button) {
    button.closest('.gallery-item').remove();
}

// Tags functionality
function focusTagInput() {
    document.getElementById('tagInputField').focus();
}

document.getElementById('tagInputField').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag(this.value.trim());
        this.value = '';
    }
});

function addTag(tagText) {
    if (tagText && !tags.includes(tagText)) {
        tags.push(tagText);
        updateTagsDisplay();
        updateTagsHidden();
    }
}

function removeTag(tagText) {
    tags = tags.filter(tag => tag !== tagText);
    updateTagsDisplay();
    updateTagsHidden();
}

function updateTagsDisplay() {
    const container = document.getElementById('tagsInput');
    const input = document.getElementById('tagInputField');

    // Clear existing tags
    const existingTags = container.querySelectorAll('.tag-item');
    existingTags.forEach(tag => tag.remove());

    // Add tags
    tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.className = 'tag-item';
        tagElement.innerHTML = `${tag} <span class="tag-remove" onclick="removeTag('${tag}')">×</span>`;
        container.insertBefore(tagElement, input);
    });
}

function updateTagsHidden() {
    document.getElementById('tagsHidden').value = JSON.stringify(tags);
}

// Status change handling
document.getElementById('statusSelect').addEventListener('change', function() {
    const publishDateGroup = document.getElementById('publishDateGroup');
    if (this.value === 'published') {
        publishDateGroup.style.display = 'block';
    } else {
        publishDateGroup.style.display = 'none';
    }
});

// Form submission handling
document.getElementById('blogForm').addEventListener('submit', function(e) {
    const saveAs = e.submitter.value;
    if (saveAs) {
        document.getElementById('statusSelect').value = saveAs;
    }
});

// Initialize
updateSlugPreview(document.getElementById('slugInput').value);
document.getElementById('excerptCount').textContent = document.getElementById('excerptInput').value.length;
document.getElementById('metaTitleCount').textContent = document.getElementById('metaTitleInput').value.length;
document.getElementById('metaDescCount').textContent = document.getElementById('metaDescInput').value.length;

// Initialize publish date visibility
if (document.getElementById('statusSelect').value !== 'published') {
    document.getElementById('publishDateGroup').style.display = 'none';
}
</script>
@endpush
