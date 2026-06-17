@extends('admin.layout')

@section('title', 'Chỉnh sửa đơn hàng')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit"></i> Chỉnh sửa đơn hàng #{{ $order->order_number }}</h1>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
            @csrf
            @method('PUT')

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
                                       value="{{ old('customer_name', $order->customer_name) }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email', $order->customer_email) }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                       value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <textarea name="customer_address" class="form-control @error('customer_address') is-invalid @enderror"
                                          rows="3" required>{{ old('customer_address', $order->customer_address) }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea name="shipping_address" class="form-control" rows="3">{{ old('shipping_address', $order->shipping_address) }}</textarea>
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
                                <label class="form-label">Mã đơn hàng</label>
                                <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Trạng thái đơn hàng <span class="text-danger">*</span></label>
                                <select @disabled($order->status == 'cancelled' || $order->status == 'completed') name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="confirmed" {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Trạng thái thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Thất bại</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="COD" {{ old('payment_method', $order->payment_method) == 'COD' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                                    <option value="ONLINE" {{ old('payment_method', $order->payment_method) == 'ONLINE' ? 'selected' : '' }}>Thanh toán online</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phí vận chuyển</label>
                                <input type="number" name="shipping_fee" class="form-control"
                                       value="{{ old('shipping_fee', $order->shipping_fee) }}" min="0" step="1000">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ngày giao hàng dự kiến</label>
                                <input type="date" name="delivery_date" class="form-control"
                                       value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items (Read-only) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Sản phẩm trong đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->main_image_url)
                                                    <img src="{{ $item->product->main_image_url }}"
                                                         alt="{{ $item->product_name }}"
                                                         class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if($item->product)
                                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ number_format($item->price) }}₫
                                            @if ($item->price != $item->pv_sales)
                                               <span class="text-muted"><del>{{ number_format($item->pv_sales) }}₫</del></span>
                                            @endif

                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>{{ number_format($item->total) }}₫</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Tạm tính:</th>
                                    <th>{{ number_format($order->subtotal) }}₫</th>
                                </tr>
                                @if ($order->system_discount)
                                    <tr>
                                        <th colspan="3">Hệ thống Giảm giá:</th>
                                        <th>- {{ number_format($order->system_discount) }}₫</th>
                                    </tr>
                                @endif
                                @if ($order->discount)
                                    <tr>
                                        <th colspan="3">Giảm giá:</th>
                                        <th>- {{ number_format($order->discount) }}₫</th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="3">Phí vận chuyển:</th>
                                    <th>{{ number_format($order->shipping_fee) }}₫</th>
                                </tr>
                                <tr class="table-dark">
                                    <th colspan="3">Tổng cộng:</th>
                                    <th>{{ number_format($order->total + $order->shipping_fee) }}₫</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
