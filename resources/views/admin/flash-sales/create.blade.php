{{-- filepath: resources/views/admin/flash-sales/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create Flash Sale')
@section('page-title', 'Tạo Flash Sale mới')

@push('styles')
<style>
    .form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    .banner-preview {
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
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
    }
    .product-item {
        padding: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
        position: relative;
    }
    .product-item:hover {
        background: #f8f9fa;
        border-color: #007bff;
    }
    .product-item.selected {
        background: #e7f3ff;
        border-color: #007bff;
    }
    .product-pricing {
        display: none;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 4px;
        margin-top: 0.5rem;
        border: 1px solid #e9ecef;
    }
    .product-item.selected .product-pricing {
        display: block;
    }
    .slug-preview {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    .datetime-local {
        font-size: 0.9rem;
    }
    .pricing-calculator {
        background: #e8f4ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .example-calculation {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 4px;
        padding: 0.75rem;
        margin-top: 0.5rem;
    }

    #individualProductSelection {
        transition: opacity 0.3s ease, pointer-events 0.3s ease;
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
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Tạo Flash Sale mới</h4>
        <p class="text-muted mb-0">Tạo chương trình giảm giá flash sale</p>
    </div>
    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="{{ route('admin.flash-sales.store') }}" method="POST" enctype="multipart/form-data" id="flashSaleForm">
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
                            <label class="form-label required-field">Tên Flash Sale</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Ví dụ: Flash Sale Cuối Tuần" id="nameInput">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
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
                                      rows="4" placeholder="Mô tả chi tiết về flash sale...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Cài đặt thời gian</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time"
                                   class="form-control datetime-local @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time') }}" id="startTimeInput">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time"
                                   class="form-control datetime-local @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time') }}" id="endTimeInput">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Cài đặt giảm giá</h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label required-field">Dạng giảm giá</label>
                            <select name="discount_type" id="discountTypeSelect" class="form-select @error('discount_type') is-invalid @enderror">
                                <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Giảm theo số tiền cố định (VNĐ)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Phần trăm giảm giá (%)</label>
                            <input type="number" name="discount_percentage"
                                   class="form-control @error('discount_percentage') is-invalid @enderror"
                                   value="{{ old('discount_percentage', 10) }}"
                                   min="0" max="100" step="0.01" id="discountInput">
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        {{-- <div class="col-md-6 mb-3">
                            <label class="form-label">Số tiền giảm tối đa (VNĐ)</label>
                            <input type="number" name="max_discount_amount"
                                   class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   value="{{ old('max_discount_amount') }}"
                                   min="0" step="1000" id="maxDiscountInput"
                                   placeholder="Để trống nếu không giới hạn">
                            @error('max_discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số lượng tối đa</label>
                            <input type="number" name="max_quantity"
                                   class="form-control @error('max_quantity') is-invalid @enderror"
                                   value="{{ old('max_quantity') }}"
                                   min="1" placeholder="Để trống nếu không giới hạn">
                            @error('max_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Giảm giá</label>
                            <input type="number" name="discount_value"
                                   class="form-control @error('discount_value') is-invalid @enderror"
                                   value="{{ old('discount_value') }}"
                                   id="discountInput">
                            @error('discount_value')
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
                    <!-- Apply to All Products Option -->
                    <div class="mb-4 p-3 bg-light border rounded">
                        <div class="form-check">
                            <input type="checkbox" name="type_all" value="1"
                                   class="form-check-input" id="applyToAll"
                                   {{ old('type_all') ? 'checked' : '' }}>
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
                    <div id="individualProductSelection">
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
                                <div class="product-item" data-product-id="{{ $product->id }}"
                                     data-product-name="{{ strtolower($product->name) }}"
                                     data-product-price="{{ $product->price }}">
                                    <div class="form-check">
                                        <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                               class="form-check-input product-checkbox" id="product_{{ $product->id }}"
                                               {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
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
                            <span id="selectedCount">0</span> sản phẩm được chọn
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Banner Upload -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Banner Flash Sale</h5>
                </div>
                <div class="p-4">
                    <div class="banner-preview" id="bannerPreview">
                        <i class="fas fa-image fa-2x me-2"></i>
                        <span>Banner sẽ hiển thị ở đây</span>
                    </div>

                    <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror"
                           accept="image/*" id="bannerInput">
                    @error('banner_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, PNG, WEBP (max 5MB)</small>
                </div>
            </div>

            <!-- Status Settings -->
            <div class="form-section">
                <div class="form-section-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Cài đặt trạng thái</h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label required-field">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Kích hoạt</option>
                            <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-bolt me-2"></i>Tạo Flash Sale
                </button>
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
    const preview = document.getElementById('slugPreview');
    preview.textContent = `URL: /flash-sales/${slug || 'your-slug'}`;
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

// Product selection and pricing calculation
function calculateSalePrice(originalPrice, discountPercentage, maxDiscountAmount) {
    let discountAmount = (originalPrice * discountPercentage) / 100;

    if (maxDiscountAmount && discountAmount > maxDiscountAmount) {
        discountAmount = maxDiscountAmount;
    }

    return originalPrice - discountAmount;
}

function updateProductPricing() {
    const discountPercentage = parseFloat(document.getElementById('discountInput').value) || 0;
    const maxDiscountAmount = parseFloat(document.getElementById('maxDiscountInput').value) || null;

    document.querySelectorAll('.sale-price').forEach(priceElement => {
        const originalPrice = parseFloat(priceElement.dataset.original);
        const salePrice = calculateSalePrice(originalPrice, discountPercentage, maxDiscountAmount);
        priceElement.textContent = new Intl.NumberFormat('vi-VN').format(salePrice) + 'đ';
    });

    // Update calculator
    updatePricingCalculator();
}

function updatePricingCalculator() {
    const originalPrice = parseFloat(document.getElementById('originalPrice').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discountInput').value) || 0;
    const maxDiscountAmount = parseFloat(document.getElementById('maxDiscountInput').value) || null;

    const salePrice = calculateSalePrice(originalPrice, discountPercentage, maxDiscountAmount);
    const savings = originalPrice - salePrice;

    document.getElementById('calculationResult').innerHTML = `
        <strong>Giá sau giảm: ${new Intl.NumberFormat('vi-VN').format(salePrice)}đ</strong><br>
        <small class="text-muted">Tiết kiệm: ${new Intl.NumberFormat('vi-VN').format(savings)}đ</small>
    `;
}

// Apply to All Products functionality
document.getElementById('applyToAll').addEventListener('change', function() {
    const individualSelection = document.getElementById('individualProductSelection');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    if (this.checked) {
        // Hide individual product selection
        individualSelection.style.opacity = '0.5';
        individualSelection.style.pointerEvents = 'none';

        // Uncheck all individual products
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.product-item').classList.remove('selected');
        });

        // Update count to show "All products"
        document.getElementById('selectedCount').innerHTML = '<strong class="text-primary">Tất cả sản phẩm</strong>';

        // Add a visual indicator
        const indicator = document.createElement('div');
        indicator.id = 'allProductsIndicator';
        indicator.className = 'alert alert-info mt-3';
        indicator.innerHTML = '<i class="fas fa-info-circle me-2"></i>Flash sale sẽ áp dụng cho tất cả sản phẩm trong hệ thống';
        individualSelection.appendChild(indicator);

    } else {
        // Show individual product selection
        individualSelection.style.opacity = '1';
        individualSelection.style.pointerEvents = 'auto';

        // Remove indicator
        const indicator = document.getElementById('allProductsIndicator');
        if (indicator) {
            indicator.remove();
        }

        // Update count
        updateSelectedCount();
    }
});

// Select All Products in List (different from Apply to All)
document.getElementById('selectAllProducts').addEventListener('change', function() {
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const applyToAllCheckbox = document.getElementById('applyToAll');

    // Don't allow if "Apply to All" is checked
    if (applyToAllCheckbox.checked) {
        this.checked = false;
        return;
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        const productItem = checkbox.closest('.product-item');
        if (this.checked) {
            productItem.classList.add('selected');
        } else {
            productItem.classList.remove('selected');
        }
    });

    updateSelectedCount();
});

// Update product checkbox handling
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const applyToAllCheckbox = document.getElementById('applyToAll');

        // Don't allow individual selection if "Apply to All" is checked
        if (applyToAllCheckbox.checked) {
            this.checked = false;
            return;
        }

        const productItem = this.closest('.product-item');
        if (this.checked) {
            productItem.classList.add('selected');
        } else {
            productItem.classList.remove('selected');
            // Uncheck "Select All" if any item is unchecked
            document.getElementById('selectAllProducts').checked = false;
        }
        updateSelectedCount();

        // Check "Select All" if all items are selected
        const totalCheckboxes = document.querySelectorAll('.product-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked').length;
        if (totalCheckboxes === checkedCheckboxes) {
            document.getElementById('selectAllProducts').checked = true;
        }
    });
});

// Update selected count function
function updateSelectedCount() {
    const applyToAllCheckbox = document.getElementById('applyToAll');

    if (applyToAllCheckbox.checked) {
        document.getElementById('selectedCount').innerHTML = '<strong class="text-primary">Tất cả sản phẩm</strong>';
    } else {
        const checkedProducts = document.querySelectorAll('input[name="products[]"]:checked');
        document.getElementById('selectedCount').textContent = checkedProducts.length;
    }
}

// Discount input handlers
document.getElementById('discountInput').addEventListener('input', updateProductPricing);
document.getElementById('maxDiscountInput').addEventListener('input', updateProductPricing);
document.getElementById('originalPrice').addEventListener('input', updatePricingCalculator);

// Set minimum datetime to current time
const now = new Date();
const currentDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
document.getElementById('startTimeInput').min = currentDateTime;

// Update end time minimum when start time changes
document.getElementById('startTimeInput').addEventListener('change', function() {
    document.getElementById('endTimeInput').min = this.value;
});

// Form validation update
document.getElementById('flashSaleForm').addEventListener('submit', function(e) {
    const startTime = new Date(document.getElementById('startTimeInput').value);
    const endTime = new Date(document.getElementById('endTimeInput').value);
    const now = new Date();
    const applyToAll = document.getElementById('applyToAll').checked;
    const selectedProducts = document.querySelectorAll('input[name="products[]"]:checked').length;

    if (startTime <= now) {
        e.preventDefault();
        alert('Thời gian bắt đầu phải sau thời điểm hiện tại!');
        return false;
    }

    if (!applyToAll && selectedProducts === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một sản phẩm hoặc chọn "Áp dụng cho tất cả sản phẩm"!');
        return false;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const applyToAllCheckbox = document.getElementById('applyToAll');
    if (applyToAllCheckbox.checked) {
        applyToAllCheckbox.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
