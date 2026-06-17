@extends('admin.layout')

@section('title', 'Tạo đơn hàng mới')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus-circle"></i> Tạo đơn hàng mới</h1>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
            @csrf

            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email') }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                       value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <textarea name="customer_address" class="form-control @error('customer_address') is-invalid @enderror"
                                          rows="3" required>{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea name="shipping_address" class="form-control" rows="3"
                                          placeholder="Để trống nếu giống địa chỉ khách hàng">{{ old('shipping_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-shopping-cart"></i> Thông tin đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Liên kết tài khoản</label>
                                <select name="user_id" class="form-select">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Chọn phương thức</option>
                                    <option value="COD" {{ old('payment_method') == 'COD' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                                    <option value="ONLINE" {{ old('payment_method') == 'ONLINE' ? 'selected' : '' }}>Thanh toán online</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                $shippingFee = old('shipping_fee',\App\Models\Setting::getSettings()->shipping_fee);
                            @endphp
                            <div class="mb-3">
                                <label class="form-label">Phí vận chuyển</label>
                                <input type="number" name="shipping_fee" class="form-control"
                                       value="{{ $shippingFee }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="notes" class="form-control" rows="3"
                                          placeholder="Ghi chú thêm về đơn hàng...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list"></i> Sản phẩm trong đơn hàng</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                        <i class="fas fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <!-- Items will be added here -->
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Tạm tính:</strong></td>
                                    <td class="text-end"><strong id="subtotal">0₫</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Phí vận chuyển:</strong></td>
                                    <td class="text-end"><strong id="shipping">0₫</strong></td>
                                </tr>
                                <tr class="table-dark">
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong id="total">0₫</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="text-end">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Tạo đơn hàng
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Item Template -->
<template id="itemTemplate">
    <div class="row mb-3 order-item">
        <div class="col-md-4">
            <select name="items[][product_id]" class="form-select product-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                            data-stock="{{ $product->stock }}">
                        {{ $product->name }} - {{ number_format($product->price) }}₫ (Còn: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[][quantity]" class="form-control quantity-input"
                   placeholder="Số lượng" min="1" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[][price]" class="form-control price-input"
                   placeholder="Đơn giá" min="0" step="1000" required>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control subtotal-display" readonly placeholder="Thành tiền">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemCount = 0;

    // Add new item
    $('#addItemBtn').click(function() {
        addOrderItem();
    });

    // Add first item automatically
    addOrderItem();

    function addOrderItem() {
        const template = $('#itemTemplate').html();
        const item = $(template);
        item.attr('data-item-id', itemCount++);
        $('#orderItems').append(item);
        updateCalculations();
    }

    // Remove item
    $(document).on('click', '.remove-item', function() {
        if ($('.order-item').length > 1) {
            $(this).closest('.order-item').remove();
            updateCalculations();
        } else {
            alert('Đơn hàng phải có ít nhất 1 sản phẩm!');
        }
    });

    // Product selection change
    $(document).on('change', '.product-select', function() {
        const option = $(this).find('option:selected');
        const price = option.data('price') || 0;
        const stock = option.data('stock') || 0;
        const item = $(this).closest('.order-item');

        item.find('.price-input').val(price);
        item.find('.quantity-input').attr('max', stock);

        if (stock == 0) {
            item.find('.quantity-input').val('').attr('disabled', true);
            alert('Sản phẩm này đã hết hàng!');
        } else {
            item.find('.quantity-input').attr('disabled', false);
            if (!item.find('.quantity-input').val()) {
                item.find('.quantity-input').val(1);
            }
        }

        updateItemSubtotal(item);
    });

    // Quantity or price change
    $(document).on('input', '.quantity-input, .price-input', function() {
        const item = $(this).closest('.order-item');
        updateItemSubtotal(item);
    });

    // Shipping fee change
    $('input[name="shipping_fee"]').on('input', function() {
        updateCalculations();
    });

    function updateItemSubtotal(item) {
        const quantity = parseInt(item.find('.quantity-input').val()) || 0;
        const price = parseFloat(item.find('.price-input').val()) || 0;
        const subtotal = quantity * price;

        item.find('.subtotal-display').val(formatNumber(subtotal) + '₫');
        updateCalculations();
    }

    function updateCalculations() {
        let subtotal = 0;

        $('.order-item').each(function() {
            const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            subtotal += quantity * price;
        });

        const shipping = parseFloat($('input[name="shipping_fee"]').val()) || 0;
        const total = subtotal + shipping;

        $('#subtotal').text(formatNumber(subtotal) + '₫');
        $('#shipping').text(formatNumber(shipping) + '₫');
        $('#total').text(formatNumber(total) + '₫');
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    // Form validation
    $('#orderForm').submit(function(e) {
        const items = $('.order-item').length;
        if (items === 0) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất 1 sản phẩm vào đơn hàng!');
            return false;
        }

        let hasError = false;
        $('.order-item').each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('.quantity-input').val();
            const price = $(this).find('.price-input').val();

            if (!productId || !quantity || !price) {
                hasError = true;
                return false;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin cho tất cả sản phẩm!');
            return false;
        }
    });
});
</script>
@endpush
