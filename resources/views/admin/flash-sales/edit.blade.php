@extends('admin.layout')

@section('title', 'Edit Flash Sale')
@section('page-title', 'Chỉnh sửa Flash Sale')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .form-section-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .banner-preview {
        max-width: 200px;
        max-height: 120px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }
    .current-banner {
        position: relative;
        display: inline-block;
    }
    .remove-banner {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 12px;
        cursor: pointer;
    }
    .product-selection {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.5rem;
    }
    .product-item {
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .product-item:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }
    .product-item.selected {
        border-color: #007bff;
        background-color: #e7f3ff;
    }
    .product-pricing {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e9ecef;
        display: none;
    }
    .product-item.selected .product-pricing {
        display: block;
    }
    .slug-preview {
        font-family: monospace;
        font-size: 0.875rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    .apply-all-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border: 2px dashed #2196f3;
        border-radius: 8px;
    }
    .apply-all-section:hover {
        border-color: #1976d2;
        background: linear-gradient(135deg, #e1f5fe 0%, #f1e6ff 100%);
    }
    .disabled-selection {
        opacity: 0.5;
        pointer-events: none;
    }
    #allProductsIndicator {
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .flash-sale-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stat-item {
        text-align: center;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    .pricing-calculator {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1rem;
        border-left: 4px solid #007bff;
    }
    .pricing-calculator h6 {
        color: #495057;
        margin-bottom: 1rem;
    }
    #calculationResult {
        background: white;
        border: 1px solid #dee2e6;
        padding: 1rem;
        border-radius: 6px;
        min-height: 60px;
        display: flex;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Chỉnh sửa Flash Sale</h4>
        <p class="text-muted mb-0">Cập nhật thông tin flash sale: {{ $flashSale->name }}</p>
    </div>
    <div>
        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <button type="submit" form="flashSaleForm" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Cập nhật Flash Sale
        </button>
    </div>
</div>
<form action="{{ route('admin.flash-sales.update', $flashSale) }}" method="POST" enctype="multipart/form-data" id="flashSaleForm">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="form-section">
        <div class="form-section-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nameInput" class="form-label required-field">Tên Flash Sale</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="nameInput"
                               value="{{ old('name', $flashSale->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slugInput" class="form-label required-field">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" id="slugInput"
                               value="{{ old('slug', $flashSale->slug) }}" required>
                        <div class="slug-preview" id="slugPreview">
                            {{ url('/flash-sale') }}/{{ old('slug', $flashSale->slug) }}
                        </div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descriptionInput" class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  id="descriptionInput" rows="3" placeholder="Mô tả ngắn về flash sale...">{{ old('description', $flashSale->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="bannerInput" class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror"
                               id="bannerInput" accept="image/*">
                        @error('banner_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($flashSale->banner_url)
                            <div class="mt-3">
                                <label class="form-label">Banner hiện tại:</label>
                                <div class="current-banner">
                                    <img src="{{ $flashSale->banner_url }}" width="200" alt="Current banner" class="banner-preview">
                                    <button type="button" class="remove-banner" onclick="removeBanner()" title="Xóa banner">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_banner" id="removeBannerInput" value="0">
                            </div>
                        @endif
                        <small class="text-muted">Kích thước đề xuất: 1900x800</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="featuredInput"
                                   {{ old('is_featured', $flashSale->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featuredInput">
                                <i class="fas fa-star text-warning me-1"></i>Flash Sale nổi bật
                            </label>
                        </div>
                        <small class="text-muted">Flash sale nổi bật sẽ được hiển thị ưu tiên</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Settings -->
    <div class="form-section">
        <div class="form-section-header">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Thời gian diễn ra</h5>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="startTimeInput" class="form-label required-field">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                               id="startTimeInput" value="{{ old('start_time', $flashSale->start_time?->format('Y-m-d\TH:i')) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="endTimeInput" class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                               id="endTimeInput" value="{{ old('end_time', $flashSale->end_time?->format('Y-m-d\TH:i')) }}">
                        <small class="text-muted">Để trống nếu không có thời gian kết thúc</small>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @if($flashSale->time_remaining)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Thời gian còn lại:</strong> {{ $flashSale->time_remaining }}
                </div>
            @endif
        </div>
    </div>

    <!-- Discount Settings -->
    <div class="form-section">
        <div class="form-section-header">
            <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Cài đặt giảm giá</h5>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required-field">Loại giảm giá</label>
                    <select name="discount_type" id="discountTypeSelect" class="form-select @error('discount_type') is-invalid @enderror">
                        <option value="percent" {{ old('discount_type', $flashSale->discount_type ?? 'percent') === 'percent' ? 'selected' : '' }}>
                            Giảm theo phần trăm (%)
                        </option>
                        <option value="fixed" {{ old('discount_type', $flashSale->discount_type) === 'fixed' ? 'selected' : '' }}>
                            Giảm số tiền cố định (VNĐ)
                        </option>
                    </select>
                    @error('discount_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-field" id="discountValueLabel">
                        <span id="discountLabelText">
                            {{ old('discount_type', $flashSale->discount_type ?? 'percent') === 'percent' ? 'Phần trăm giảm giá (%)' : 'Số tiền giảm (VNĐ)' }}
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="discount_value" id="discountValueInput"
                               class="form-control @error('discount_value') is-invalid @enderror"
                               value="{{ old('discount_value', $flashSale->discount_value) }}"
                               min="0" step="0.01" placeholder="Nhập giá trị giảm giá" required>
                        <span class="input-group-text" id="discountUnit">
                            {{ old('discount_type', $flashSale->discount_type ?? 'percent') === 'percent' ? '%' : 'đ' }}
                        </span>
                    </div>
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted" id="discountHelp">
                        {{ old('discount_type', $flashSale->discount_type ?? 'percent') === 'percent' ? 'Nhập phần trăm giảm giá từ 0 đến 100' : 'Nhập số tiền giảm giá cố định' }}
                    </small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Số lượng tối đa</label>
                    <input type="number" name="max_quantity"
                           class="form-control @error('max_quantity') is-invalid @enderror"
                           value="{{ old('max_quantity', $flashSale->max_quantity) }}"
                           min="1" placeholder="Để trống nếu không giới hạn">
                    @error('max_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Tổng số lượng sản phẩm có thể mua với giá flash sale</small>
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
            <!-- Apply to All Products Option -->
            <div class="mb-4 p-3 apply-all-section">
                <div class="form-check">
                    <input type="checkbox" name="type_all" value="1"
                           class="form-check-input" id="applyToAll"
                           {{ old('type_all', $flashSale->type_all) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="applyToAll">
                        <i class="fas fa-globe me-2 text-primary"></i>
                        Áp dụng cho tất cả sản phẩm
                    </label>
                </div>
                <small class="text-muted d-block mt-1">
                    Khi chọn tùy chọn này, flash sale sẽ áp dụng cho toàn bộ sản phẩm trong cửa hàng
                </small>
            </div>

            <!-- Individual Product Selection -->
            <div id="individualProductSelection" class="{{ old('type_all', $flashSale->type_all) ? 'disabled-selection' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Hoặc chọn sản phẩm cụ thể:</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAllProducts">
                        <label class="form-check-label" for="selectAllProducts">
                            <small>Chọn tất cả trong danh sách</small>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." id="productSearch">
                </div>

                <div class="product-selection" id="productList">
                    @foreach($products as $product)
                        <div class="product-item {{ in_array($product->id, old('products', $flashSale->products->pluck('id')->toArray())) ? 'selected' : '' }}"
                             data-product-id="{{ $product->id }}"
                             data-product-name="{{ strtolower($product->name) }}"
                             data-product-price="{{ $product->price }}">
                            <div class="form-check">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                       class="form-check-input product-checkbox" id="product_{{ $product->id }}"
                                       {{ in_array($product->id, old('products', $flashSale->products->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="product_{{ $product->id }}">
                                    <div class="d-flex align-items-center">
                                        @if($product->main_image_url)
                                            <img src="{{ $product->main_image_url }}"
                                                 alt="{{ $product->name }}" class="me-3"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                        @else
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; border-radius: 6px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <strong>{{ $product->name }}</strong>
                                            <br>
                                            <span class="text-primary fw-bold">{{ number_format($product->price) }}đ</span>
                                            @if($product->category)
                                                <small class="text-muted"> • {{ $product->category->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Product Pricing Details -->
                            <div class="product-pricing">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Giá gốc:</small>
                                        <div class="fw-bold">{{ number_format($product->price) }}đ</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Giá sau giảm:</small>
                                        <div class="fw-bold text-danger sale-price" data-original="{{ $product->price }}">
                                            0đ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 text-muted">
                    <span id="selectedCount">
                        {{ old('type_all', $flashSale->type_all) ? 'Tất cả sản phẩm' : count(old('products', $flashSale->products->pluck('id')->toArray())) }}
                    </span>
                    {{ old('type_all', $flashSale->type_all) ? '' : 'sản phẩm được chọn' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Status Settings -->
    <div class="form-section">
        <div class="form-section-header">
            <h5 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Trạng thái</h5>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="statusInput" class="form-label required-field">Trạng thái</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" id="statusInput" required>
                        <option value="draft" {{ old('status', $flashSale->status) === 'draft' ? 'selected' : '' }}>Nháp</option>
                        <option value="active" {{ old('status', $flashSale->status) === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="paused" {{ old('status', $flashSale->status) === 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                        <option value="ended" {{ old('status', $flashSale->status) === 'ended' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
    document.getElementById('slugPreview').textContent = `{{ url('/flash-sale') }}/${slug}`;
}

// Banner preview
document.getElementById('bannerInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('bannerPreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="fas fa-image fa-2x me-2"></i><span>Banner sẽ hiển thị ở đây</span>';
    }
});

function removeBanner() {
    document.getElementById('removeBannerInput').value = '1';
    document.querySelector('.current-banner').style.display = 'none';
}

// Discount Type Change Handler
document.getElementById('discountTypeSelect').addEventListener('change', function() {
    const discountType = this.value;
    const discountValueInput = document.getElementById('discountValueInput');
    const discountUnit = document.getElementById('discountUnit');
    const discountLabelText = document.getElementById('discountLabelText');
    const discountHelp = document.getElementById('discountHelp');
    const maxDiscountSection = document.getElementById('maxDiscountSection');

    if (discountType === 'percent') {
        // Percentage settings
        discountLabelText.textContent = 'Phần trăm giảm giá (%)';
        discountUnit.textContent = '%';
        discountValueInput.setAttribute('max', '100');
        discountValueInput.setAttribute('step', '0.01');
        discountValueInput.setAttribute('placeholder', 'Ví dụ: 20 (giảm 20%)');
        discountHelp.textContent = 'Nhập phần trăm giảm giá từ 0 đến 100';
        maxDiscountSection.style.display = 'block';
    } else {
        // Fixed amount settings
        discountLabelText.textContent = 'Số tiền giảm (VNĐ)';
        discountUnit.textContent = 'đ';
        discountValueInput.removeAttribute('max');
        discountValueInput.setAttribute('step', '1000');
        discountValueInput.setAttribute('placeholder', 'Ví dụ: 50000 (giảm 50,000đ)');
        discountHelp.textContent = 'Nhập số tiền giảm giá cố định';
        maxDiscountSection.style.display = 'none';
    }

    // Recalculate
    updateProductPricing();
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

// Enhanced Price Calculation
function calculateSalePrice(originalPrice, discountType, discountValue, maxDiscountAmount = null) {
    let discountAmount = 0;
    let salePrice = originalPrice;

    if (discountType === 'percentage') {
        discountAmount = (originalPrice * discountValue) / 100;

        // Apply max discount limit if set
        if (maxDiscountAmount && discountAmount > maxDiscountAmount) {
            discountAmount = maxDiscountAmount;
        }
    } else if (discountType === 'fixed') {
        discountAmount = discountValue;

        // Don't let discount exceed original price
        if (discountAmount > originalPrice) {
            discountAmount = originalPrice;
        }
    }

    salePrice = originalPrice - discountAmount;
    return {
        salePrice: Math.max(0, salePrice),
        discountAmount: discountAmount,
        savingsPercentage: originalPrice > 0 ? (discountAmount / originalPrice) * 100 : 0
    };
}

function updateProductPricing() {
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountValue = parseFloat(document.getElementById('discountValueInput').value) || 0;
    const maxDiscountAmount = parseFloat(document.getElementById('maxDiscountInput')?.value) || null;

    // Update all product pricing displays
    document.querySelectorAll('.sale-price').forEach(priceElement => {
        const originalPrice = parseFloat(priceElement.dataset.original);
        const result = calculateSalePrice(originalPrice, discountType, discountValue, maxDiscountAmount);

        priceElement.innerHTML = `
            <span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(result.salePrice)}đ</span>
            <br><small class="text-success">Tiết kiệm: ${result.savingsPercentage.toFixed(1)}%</small>
        `;
    });

    // Update calculator
    updatePricingCalculator();
}

function updatePricingCalculator() {
    const originalPrice = parseFloat(document.getElementById('originalPriceCalculator').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountValue = parseFloat(document.getElementById('discountValueInput').value) || 0;
    const maxDiscountAmount = parseFloat(document.getElementById('maxDiscountInput')?.value) || null;

    if (originalPrice > 0 && discountValue > 0) {
        const result = calculateSalePrice(originalPrice, discountType, discountValue, maxDiscountAmount);

        document.getElementById('calculationResult').innerHTML = `
            <strong class="text-danger">Giá sau giảm: ${new Intl.NumberFormat('vi-VN').format(result.salePrice)}đ</strong><br>
            <small class="text-success">Tiết kiệm: ${new Intl.NumberFormat('vi-VN').format(result.discountAmount)}đ (${result.savingsPercentage.toFixed(1)}%)</small>
        `;
    } else {
        document.getElementById('calculationResult').innerHTML = `
            <strong>Giá sau giảm: 0đ</strong><br>
            <small class="text-muted">Nhập giá gốc và giá trị giảm giá để tính toán</small>
        `;
    }
}

// Apply to All Products functionality
document.getElementById('applyToAll').addEventListener('change', function() {
    const individualSelection = document.getElementById('individualProductSelection');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    if (this.checked) {
        individualSelection.classList.add('disabled-selection');
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.product-item').classList.remove('selected');
        });
        document.getElementById('selectedCount').innerHTML = '<strong class="text-primary">Tất cả sản phẩm</strong>';

        // Add indicator
        if (!document.getElementById('allProductsIndicator')) {
            const indicator = document.createElement('div');
            indicator.id = 'allProductsIndicator';
            indicator.className = 'alert alert-info mt-3';
            indicator.innerHTML = '<i class="fas fa-info-circle me-2"></i>Flash sale sẽ áp dụng cho tất cả sản phẩm trong hệ thống';
            individualSelection.appendChild(indicator);
        }
    } else {
        individualSelection.classList.remove('disabled-selection');
        const indicator = document.getElementById('allProductsIndicator');
        if (indicator) indicator.remove();
        updateSelectedCount();
    }
});

// Select All Products in List
document.getElementById('selectAllProducts').addEventListener('change', function() {
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const applyToAllCheckbox = document.getElementById('applyToAll');

    if (applyToAllCheckbox.checked) {
        this.checked = false;
        return;
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        const productItem = checkbox.closest('.product-item');
        productItem.classList.toggle('selected', this.checked);
    });

    updateSelectedCount();
});

// Individual product checkbox handling
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const applyToAllCheckbox = document.getElementById('applyToAll');

        if (applyToAllCheckbox.checked) {
            this.checked = false;
            return;
        }

        const productItem = this.closest('.product-item');
        productItem.classList.toggle('selected', this.checked);

        if (!this.checked) {
            document.getElementById('selectAllProducts').checked = false;
        }

        updateSelectedCount();

        // Check "Select All" if all items are selected
        const totalCheckboxes = document.querySelectorAll('.product-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked').length;
        document.getElementById('selectAllProducts').checked = (totalCheckboxes === checkedCheckboxes);
    });
});

function updateSelectedCount() {
    const applyToAllCheckbox = document.getElementById('applyToAll');

    if (applyToAllCheckbox.checked) {
        document.getElementById('selectedCount').innerHTML = '<strong class="text-primary">Tất cả sản phẩm</strong>';
    } else {
        const checkedProducts = document.querySelectorAll('input[name="products[]"]:checked');
        document.getElementById('selectedCount').textContent = checkedProducts.length + ' sản phẩm được chọn';
    }
}

// Event listeners for real-time calculation
document.getElementById('discountValueInput').addEventListener('input', updateProductPricing);
document.getElementById('maxDiscountInput')?.addEventListener('input', updateProductPricing);
document.getElementById('originalPriceCalculator').addEventListener('input', updatePricingCalculator);

// DateTime validation
document.getElementById('startTimeInput').addEventListener('change', function() {
    document.getElementById('endTimeInput').min = this.value;
});

// Enhanced form validation
document.getElementById('flashSaleForm').addEventListener('submit', function(e) {
    const startTime = new Date(document.getElementById('startTimeInput').value);
    const endTime = new Date(document.getElementById('endTimeInput').value);
    const applyToAll = document.getElementById('applyToAll').checked;
    const selectedProducts = document.querySelectorAll('input[name="products[]"]:checked').length;
    const discountValue = parseFloat(document.getElementById('discountValueInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;

    // Validate end time
    if (endTime && endTime <= startTime) {
        e.preventDefault();
        alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
        return false;
    }

    // Validate product selection
    if (!applyToAll && selectedProducts === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một sản phẩm hoặc chọn "Áp dụng cho tất cả sản phẩm"!');
        return false;
    }

    // Validate discount value
    if (discountValue <= 0) {
        e.preventDefault();
        alert('Vui lòng nhập giá trị giảm giá hợp lệ!');
        return false;
    }

    // Validate percentage discount
    if (discountType === 'percentage' && discountValue > 100) {
        e.preventDefault();
        alert('Phần trăm giảm giá không được vượt quá 100%!');
        return false;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger discount type change to set initial state
    document.getElementById('discountTypeSelect').dispatchEvent(new Event('change'));

    // Initialize apply to all if checked
    const applyToAllCheckbox = document.getElementById('applyToAll');
    if (applyToAllCheckbox.checked) {
        applyToAllCheckbox.dispatchEvent(new Event('change'));
    }

    // Initial calculation
    updateProductPricing();
    updatePricingCalculator();
});
</script>
@endpush
