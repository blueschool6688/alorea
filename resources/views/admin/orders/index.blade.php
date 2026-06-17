@extends('admin.layout')

@section('title', 'Quản lý đơn hàng')

@push('styles')
<style>
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        body {
            font-size: 12px;
            line-height: 1.4;
        }
        .table {
            font-size: 11px;
        }
    }
    .company-logo {
        max-height: 60px;
        margin-bottom: 10px;
    }
    .print-only {
        display: none;
    }
    @media print {
        .price-original {
            text-decoration: line-through;
            color: #888 !important;
            font-size: 11px;
            margin-right: 5px;
        }

        .price-sale {
            color: #28a745 !important;
            font-weight: bold;
        }

        .price-discount {
            color: #e74c3c !important;
            font-size: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('admin.orders.export-excel') }}" class="d-inline">
                    <!-- Preserve current filters for export -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('payment_status'))
                        <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
                    @endif
                    @if(request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if(request('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif

                    <button type="submit" class="btn btn-success" title="Xuất Excel">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </button>
                </form>

                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo đơn hàng
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Mã đơn, tên, email, số điện thoại..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Giao hàng</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Thanh toán</label>
                            <select name="payment_status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Orders Table -->
        <div class="card" id="printableArea">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Tiền tạm tính</th>
                                <th>Giảm giá</th>
                                <th>Phí vận chuyển</th>
                                <th>Hệ thống giảm giá</th>
                                <th>Tổng tiền</th>
                                <th>Phương thức thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Ngày tạo</th>
                                <th class="no-print">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $order->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $order->customer_email }}</small><br>
                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($order->subtotal) }}₫
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($order->discount) }}₫
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($order->shipping_fee) }}₫
                                    </td>
                                     <td class="fw-bold text-primary">
                                        {{ number_format($order->system_discount) }}₫
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($order->total + $order->shipping_fee) }}₫
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ $order->payment_method }}
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select no-print"
                                                {{$order->status == 'cancelled' || $order->status == 'completed' ? 'disabled' : '' }}
                                                data-order-id="{{ $order->id }}"
                                                data-current-status="{{ $order->status }}">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        </select>
                                        <!-- Print version of status -->
                                        <span class="print-only">
                                            @php
                                                $statusTexts = [
                                                    'pending' => 'Chờ xử lý',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'processing' => 'Đang xử lý',
                                                    'shipped' => 'Đã giao',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                            @endphp
                                            {{ $statusTexts[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $paymentBadges = [
                                                'pending' => 'bg-warning',
                                                'paid' => 'bg-success',
                                                'failed' => 'bg-danger',
                                                'refunded' => 'bg-info'
                                            ];
                                            $paymentTexts = [
                                                'pending' => 'Chờ thanh toán',
                                                'paid' => 'Đã thanh toán',
                                                'failed' => 'Thất bại',
                                                'refunded' => 'Đã hoàn tiền'
                                            ];
                                        @endphp
                                        <span class="badge {{ $paymentBadges[$order->payment_status] ?? 'bg-secondary' }} no-print">
                                            {{ $paymentTexts[$order->payment_status] ?? $order->payment_status }}
                                        </span>
                                        <!-- Print version of payment status -->
                                        <span class="print-only">
                                            {{ $paymentTexts[$order->payment_status] ?? $order->payment_status }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="no-print">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.edit', $order) }}"
                                               class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- <a href="{{ route('admin.orders.download-pdf', $order) }}"
                                                class="btn btn-sm btn-info" title="Tải xuống hóa đơn"
                                                target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                            </a> --}}
                                            <!-- Print individual order button -->
                                            <button type="button" class="btn btn-sm btn-secondary print-single-order"
                                                    data-order-id="{{ $order->id }}"
                                                    data-order-number="{{ $order->order_number }}"
                                                    data-full-address ="{{ $order->full_address }}"
                                                    title="In đơn hàng">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            @if(in_array($order->status, ['pending', 'cancelled']))
                                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                                      class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Không có đơn hàng nào được tìm thấy</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center mt-4 no-print">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery Print Plugin CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>

<script>
$(document).ready(function() {

    function formatVND(price) {
        if (!price || isNaN(price)) return '0₫';

        // Convert to number and format with thousands separator
        const number = parseFloat(price);
        return number.toLocaleString('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).replace('₫', '₫');
    }
    // Print all orders with invoice style
    $('#printOrdersBtn').click(function() {
        // Create invoice-style header
        var printHeader = `
            <div style="font-family: 'Arial', sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                <!-- Header -->
                <div style="display: table; width: 100%; margin-bottom: 30px; border-bottom: 2px solid #3498db; padding-bottom: 20px;">
                    <div style="display: table-cell; vertical-align: top; width: 60%;">
                        <div style="font-size: 20px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">ALORÉA</div>
                        <div style="color: #666;">123 Đường ABC, Quận XYZ, TP.HCM</div>
                        <div style="color: #666;">Điện thoại: (028) 1234-5678</div>
                        <div style="color: #666;">Email: info@alorea.com</div>
                        <div style="color: #666;">Website: www.alorea.com</div>
                    </div>
                    <div style="display: table-cell; vertical-align: top; text-align: right; width: 40%;">
                        <div style="font-size: 24px; font-weight: bold; color: #e74c3c; margin-bottom: 10px;">DANH SÁCH ĐƠN HÀNG</div>
                        <div><strong>Ngày in:</strong> ${new Date().toLocaleDateString('vi-VN')}</div>
                        <div><strong>Thời gian:</strong> ${new Date().toLocaleTimeString('vi-VN')}</div>
                    </div>
                </div>
        `;

        var printFooter = `
                <!-- Footer -->
                <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #3498db; font-size: 11px; color: #666; text-align: center;">
                    <p style="margin-bottom: 5px;"><strong>Lưu ý:</strong> Danh sách này được tạo tự động từ hệ thống.</p>
                    <p style="margin-bottom: 5px;"><strong>Hotline hỗ trợ:</strong> (028) 1234-5678</p>
                    <div style="margin-top: 20px; font-size: 14px; font-weight: bold; color: #2c3e50;">
                        Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của ALORÉA!
                    </div>
                </div>
            </div>
        `;

        $('#printableArea').print({
            globalStyles: true,
            mediaPrint: true,
            stylesheet: null,
            noPrintSelector: ".no-print",
            iframe: false,
            append: printFooter,
            prepend: printHeader,
            manuallyClosed: false
        });
    });

    // Print single order with full invoice style
     $('.print-single-order').click(function() {
        var orderId = $(this).data('order-id');
        var orderNumber = $(this).data('order-number');
        var fullAddress = $(this).data('full-address');

        // Get order data from PHP (passed to JavaScript)
        var orders = @json($orders->items()); // Get all order data including items
        var settings = @json($settings ?? []);

        // Find the current order
        var order = orders.find(o => o.id == orderId);
        if (!order) {
            alert('Không tìm thấy thông tin đơn hàng!');
            return;
        }

        // Map payment status
        var paymentStatusMap = {
            'paid': 'Đã thanh toán',
            'pending': 'Chờ thanh toán',
            'failed': 'Thất bại',
            'refunded': 'Đã hoàn tiền'
        };
        var paymentStatus = paymentStatusMap[order.payment_status] || order.payment_status;

        // Map order status
        var statusMap = {
            'pending': 'Chờ xử lý',
            'confirmed': 'Đã xác nhận',
            'processing': 'Đang xử lý',
            'shipped': 'Đã giao',
            'completed': 'Hoàn thành',
            'cancelled': 'Đã hủy'
        };
        var statusData = statusMap[order.status] || order.status;

        var printContent = `
            <div style="font-family: 'Arial', sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; color: #333;">
                <!-- Header -->
                <div style="display: table; width: 100%; margin-bottom: 30px;">
                    ${settings.logo_url ? `<img src="${settings.logo_url}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">` : ''}
                    <div style="display: table-cell; vertical-align: top; width: 60%;">
                        <div style="font-size: 20px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">ALORÉA</div>
                        <div style="color: #666; margin-bottom: 3px;">${settings.address || '123 Đường ABC, Quận XYZ, TP.HCM'}</div>
                        <div style="color: #666; margin-bottom: 3px;">Điện thoại: ${settings.hotline || '(028) 1234-5678'}</div>
                        <div style="color: #666; margin-bottom: 3px;">Email: ${settings.email_contact || 'contact@gmail.com'}</div>
                    </div>
                    <div style="display: table-cell; vertical-align: top; text-align: right; width: 40%;">
                        <div style="font-size: 24px; font-weight: bold; color: #e74c3c; margin-bottom: 10px;">HÓA ĐƠN</div>
                        <div style="margin-bottom: 5px;"><strong>Số:</strong> ${order.order_number}</div>
                        <div style="margin-bottom: 5px;"><strong>Ngày:</strong> ${new Date(order.created_at).toLocaleDateString('vi-VN')}</div>
                        <div><strong>Trạng thái:</strong> <span style="color: #2c3e50;">${statusData}</span></div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 14px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; margin-bottom: 15px;">
                        THÔNG TIN KHÁCH HÀNG
                    </div>
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                        <div style="margin-bottom: 8px;">
                            <span style="font-weight: bold; display: inline-block; width: 120px;">Họ tên:</span>
                            ${order.customer_name}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <span style="font-weight: bold; display: inline-block; width: 120px;">Email:</span>
                            ${order.customer_email}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <span style="font-weight: bold; display: inline-block; width: 120px;">Điện thoại:</span>
                            ${order.customer_phone}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <span style="font-weight: bold; display: inline-block; width: 120px;">Địa chỉ:</span>
                            ${order.full_address || fullAddress}
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 14px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; margin-bottom: 15px;">
                        CHI TIẾT SẢN PHẨM
                    </div>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 10px; background-color: #3498db; color: white; text-align: left; width: 8%;">
                                    STT
                                </th>
                                <th style="border: 1px solid #ddd; padding: 10px; background-color: #3498db; color: white; text-align: left; width: 42%;">
                                    Sản phẩm
                                </th>
                                <th style="border: 1px solid #ddd; padding: 10px; background-color: #3498db; color: white; text-align: center; width: 12%;">
                                    Số lượng
                                </th>
                                <th style="border: 1px solid #ddd; padding: 10px; background-color: #3498db; color: white; text-align: right; width: 18%;">
                                    Đơn giá
                                </th>
                                <th style="border: 1px solid #ddd; padding: 10px; background-color: #3498db; color: white; text-align: right; width: 20%;">
                                    Thành tiền
                                </th>
                            </tr>
                        </thead>
                        <tbody>`;
        if (order.items && order.items.length > 0) {
            order.items.forEach(function(item, index) {
                var backgroundColor = index % 2 === 1 ? 'background-color: #f8f9fa;' : '';
                printContent += `
                    <tr style="${backgroundColor}">
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">
                            ${index + 1}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <div style="font-weight: bold; margin-bottom: 3px; color: #2c3e50;">${item.product_name}</div>
                            ${item.variant_name ? `<div style="font-size: 11px; color: #666; margin-bottom: 2px;">Phân loại: ${item.variant_name}</div>` : ''}
                            ${item.product_sku ? `<div style="font-size: 11px; color: #888;">SKU: ${item.product_sku}</div>` : ''}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold; color: #2c3e50;">
                            ${item.quantity}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                            <div style="color: #e74c3c; text-decoration: line-through; font-size: 11px; margin-bottom: 2px;">
                                ${formatVND(item.price)}
                            </div>
                            <div style="color: #28a745; font-weight: bold;">
                                ${formatVND(item.pv_sales)}
                            </div>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; color: #e74c3c;">
                            ${formatVND(item.price * item.quantity)}
                        </td>
                    </tr>
                `;
            });
        } else {
            printContent += `
                <tr>
                    <td colspan="5" style="border: 1px solid #ddd; padding: 20px; text-align: center; color: #666; font-style: italic;">
                        Không có sản phẩm nào trong đơn hàng
                    </td>
                </tr>
            `;
        }

        printContent += `
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 14px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; margin-bottom: 15px;">
                        THÔNG TIN THANH TOÁN
                    </div>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold; width: 70%;">Mã đơn hàng</td>
                                <td style="border: 1px solid #ddd; padding: 10px;">${order.order_number}</td>
                            </tr>
                            <tr style="background-color: #f8f9fa;">
                                <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Phương thức thanh toán</td>
                                <td style="border: 1px solid #ddd; padding: 10px;">${order.payment_method}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Trạng thái thanh toán</td>
                                <td style="border: 1px solid #ddd; padding: 10px; color: ${order.payment_status === 'paid' ? '#28a745' : '#ffc107'}; font-weight: bold;">${paymentStatus}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div style="float: right; width: 350px; margin-bottom: 30px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; font-size: 13px;">
                                Tạm tính:
                            </td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; font-size: 13px;">
                                ${formatVND(order.subtotal)}
                            </td>
                        </tr>ssss
                        <tr>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; font-size: 13px;">
                                Giảm giá:
                            </td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; font-size: 13px; color: #e74c3c;">
                                -${formatVND(order.discount)}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; font-size: 13px;">
                                Hệ thống giảm giá:
                            </td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; font-size: 13px; color: #e74c3c;">
                                -${formatVND(order.system_discount)}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; font-size: 13px;">
                                Phí vận chuyển:
                            </td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; font-size: 13px;">
                                ${formatVND(order.shipping_fee)}
                            </td>
                        </tr>
                        <tr style="background-color: #2c3e50; color: white;">
                            <td style="padding: 12px 15px; font-weight: bold; text-align: right; font-size: 15px;">
                                TỔNG CỘNG:
                            </td>
                            <td style="padding: 12px 15px; text-align: right; font-weight: bold; font-size: 15px;">
                                ${formatVND(+order.total + +order.shipping_fee)}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Notes Section -->
                ${order.notes ? `
                <div style="clear: both; margin-bottom: 30px;">
                    <div style="font-size: 14px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; margin-bottom: 15px;">
                        GHI CHÚ
                    </div>
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; font-style: italic; color: #555;">
                        ${order.notes}
                    </div>
                </div>
                ` : ''}

                <!-- Thank You Message -->
                <div style="clear: both; text-align: center; font-size: 16px; font-weight: bold; color: #2c3e50; margin: 30px 0;">
                    Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!
                </div>
            </div>
        `;

        // Create a temporary div and print it
        $('<div>').html(printContent).print({
            globalStyles: true,
            mediaPrint: true,
            stylesheet: null,
            iframe: false,
            manuallyClosed: false
        });
    });

    // Handle status change
    $('.status-select').change(function() {
        const orderId = $(this).data('order-id');
        const newStatus = $(this).val();
        const currentStatus = $(this).data('current-status');

        if (newStatus === currentStatus) return;

        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng?')) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/admin/orders/${orderId}/status`,
                method: 'PATCH',
                data: { status: newStatus },
                success: function(response) {
                    if (response.success) {
                        // Update current status
                        $(this).data('current-status', newStatus);

                        // Show success message
                        $('<div class="alert alert-success alert-dismissible fade show">' +
                          '<i class="fas fa-check-circle"></i> ' + response.message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>').prependTo('.container-fluid').delay(3000).fadeOut();

                        window.location.reload();
                    }
                }.bind(this),
                error: function() {
                    // Revert select
                    $(this).val(currentStatus);
                    alert('Có lỗi xảy ra khi cập nhật trạng thái!');
                }.bind(this)
            });
        } else {
            // Revert select
            $(this).val(currentStatus);
        }
    });
});
</script>
@endpush
