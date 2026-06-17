@extends('admin.layout')

@section('title', 'Thêm mùi hương mới')
@section('page-title', 'Thêm mùi hương')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
        cursor: pointer;
    }
    .intensity-preview {
        width: 100%;
        height: 12px;
        border-radius: 6px;
        background: #e9ecef;
        position: relative;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .intensity-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745 0%, #ffc107 50%, #dc3545 100%);
        border-radius: 6px;
        transition: width 0.3s ease;
    }
    .slug-preview {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .type-info {
        background: #f8f9fa;
        border-left: 4px solid #17a2b8;
        padding: 1rem;
        margin-top: 0.5rem;
        border-radius: 0 4px 4px 0;
    }
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .category-item {
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-size: 0.875rem;
    }
    .category-item:hover,
    .category-item.selected {
        background: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Thêm mùi hương mới</h4>
        <p class="text-muted mb-0">Tạo mùi hương cho hệ thống nước hoa</p>
    </div>
    <a href="{{ route('admin.scents.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.scents.store') }}" method="POST" id="scentForm">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Tên mùi hương</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="VD: Bergamot, Rose, Sandalwood..." id="nameInput">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="Tự động tạo từ tên" id="slugInput">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Mô tả chi tiết về mùi hương này...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Ghi chú thêm</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="2" placeholder="Ghi chú về cách sử dụng, đặc điểm đặc biệt...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scent Properties -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Thuộc tính mùi hương</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Loại Note</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" id="typeSelect">
                                <option value="">Chọn loại note</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="type-info" id="typeInfo" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Cài đặt</h5>
                </div>
                <div class="p-4">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input"
                               id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">
                            <i class="fas fa-power-off text-success me-1"></i>
                            Kích hoạt ngay
                        </label>
                        <small class="form-text text-muted d-block">Mùi hương có thể được sử dụng ngay</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.scents.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Lưu mùi hương
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Auto generate slug from name
document.getElementById('nameInput').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
                     .normalize('NFD')
                     .replace(/[\u0300-\u036f]/g, '')
                     .replace(/[^a-z0-9\s-]/g, '')
                     .replace(/\s+/g, '-')
                     .replace(/-+/g, '-')
                     .trim();

    document.getElementById('slugInput').value = slug;
    updateSlugPreview(slug);
    updatePreview();
});

// Slug input manual change
document.getElementById('slugInput').addEventListener('input', function() {
    updateSlugPreview(this.value);
});

function updateSlugPreview(slug) {
    document.getElementById('slugPreview').textContent = `URL: /scents/${slug || 'your-slug'}`;
}

// Color picker functionality
document.getElementById('colorInput').addEventListener('input', function() {
    const color = this.value;
    document.getElementById('colorText').value = color;
    document.getElementById('colorPreview').style.backgroundColor = color;
    updatePreview();
});

document.getElementById('colorText').addEventListener('input', function() {
    const color = this.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
        document.getElementById('colorInput').value = color;
        document.getElementById('colorPreview').style.backgroundColor = color;
        updatePreview();
    }
});

// Type selection with info
document.getElementById('typeSelect').addEventListener('change', function() {
    const type = this.value;
    const typeInfo = document.getElementById('typeInfo');

    const typeDescriptions = {
        'top': 'Hương đầu: Mùi hương đầu tiên được cảm nhận, thường tươi mát và bay hơi nhanh (5-15 phút).',
        'middle': 'Hương giữa: Trái tim của nước hoa, xuất hiện sau hương đầu và kéo dài 2-4 giờ.',
        'base': 'Hương cuối: Nền tảng bền vững của nước hoa, có thể kéo dài 6-8 giờ hoặc cả ngày.'
    };

    if (type && typeDescriptions[type]) {
        typeInfo.style.display = 'block';
        typeInfo.innerHTML = `<small><strong>Thông tin:</strong> ${typeDescriptions[type]}</small>`;
    } else {
        typeInfo.style.display = 'none';
    }

    updatePreview();
});

// Category selection
document.querySelectorAll('.category-item').forEach(item => {
    item.addEventListener('click', function() {
        // Remove selection from all items
        document.querySelectorAll('.category-item').forEach(i => i.classList.remove('selected'));

        // Select current item
        this.classList.add('selected');
        document.getElementById('categoryInput').value = this.dataset.value;

        updatePreview();
    });
});

// Initialize category selection
const oldCategory = '{{ old("category") }}';
if (oldCategory) {
    document.querySelector(`[data-value="${oldCategory}"]`)?.classList.add('selected');
}

// Intensity range
document.getElementById('intensityRange').addEventListener('input', function() {
    const intensity = this.value;
    document.getElementById('intensityValue').textContent = intensity;
    document.getElementById('intensityFill').style.width = (intensity * 10) + '%';
    updatePreview();
});

// Preview update
function updatePreview() {
    const name = document.getElementById('nameInput').value || 'Tên mùi hương';
    const type = document.getElementById('typeSelect').value;
    const color = document.getElementById('colorInput').value;
    const description = document.querySelector('textarea[name="description"]').value || 'Mô tả mùi hương...';
    const category = document.getElementById('categoryInput').value;
    const intensity = document.getElementById('intensityRange').value;

    // Type names
    const typeNames = {
        'top': 'Top Note (Hương đầu)',
        'middle': 'Middle Note (Hương giữa)',
        'base': 'Base Note (Hương cuối)'
    };

    // Category names
    const categoryNames = @json($categories);

    document.getElementById('previewName').textContent = name;
    document.getElementById('previewType').textContent = typeNames[type] || 'Loại note';
    document.getElementById('previewColor').style.backgroundColor = color;
    document.getElementById('previewDescription').textContent = description.substring(0, 100) + (description.length > 100 ? '...' : '');
    document.getElementById('previewCategory').textContent = categoryNames[category] || 'Danh mục';
    document.getElementById('previewIntensity').textContent = intensity;
}

// Initialize
updateSlugPreview(document.getElementById('slugInput').value);
updatePreview();

// Form validation before submit
document.getElementById('scentForm').addEventListener('submit', function(e) {
    const category = document.getElementById('categoryInput').value;
    if (!category) {
        e.preventDefault();
        alert('Vui lòng chọn danh mục mùi hương');
        return false;
    }
});

// Add description change listener
document.querySelector('textarea[name="description"]').addEventListener('input', updatePreview);
</script>
@endpush
