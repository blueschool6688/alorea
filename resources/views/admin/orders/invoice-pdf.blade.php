<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .company-info {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .invoice-info {
            display: table-cell;
            vertical-align: top;
            text-align: right;
            width: 40%;
        }

        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .customer-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .customer-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .items-table th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .totals-section {
            float: right;
            width: 300px;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }

        .totals-table .total-label {
            font-weight: bold;
            text-align: right;
        }

        .totals-table .total-value {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            background-color: #2c3e50;
            color: white;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }

        .status-pending { background-color: #f39c12; }
        .status-confirmed { background-color: #3498db; }
        .status-processing { background-color: #9b59b6; }
        .status-shipped { background-color: #1abc9c; }
        .status-completed { background-color: #27ae60; }
        .status-cancelled { background-color: #e74c3c; }

        .footer-info {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #3498db;
            font-size: 11px;
            color: #666;
        }

        .footer-info p {
            margin-bottom: 5px;
        }

        .thank-you {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                @if($settings && $settings->logo_url)
                    <img src="{{ $settings->logo_url }}"
                         alt="Logo" class="company-logo">
                @endif
                <div class="company-name">
                  ALORÉA
                </div>
                <div>{{ $settings->address ?? 'Địa chỉ công ty' }}</div>
                <div>Điện thoại: {{ $settings->hotline ?? 'N/A' }}</div>
                <div>Email: {{ $settings->email_contact ?? 'contact@alorea.com' }}</div>
                @if($settings->website)
                    <div>Website: {{ env('APP_URL') }}</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-title">HÓA ĐƠN</div>
                <div><strong>Số:</strong> {{ $order->order_number }}</div>
                <div><strong>Ngày:</strong> {{ $order->created_at->format('d/m/Y') }}</div>
                <div><strong>Trạng thái:</strong>
                <span >
                    @switch($order->status)
                        @case('pending') Chờ xử lý @break
                        @case('confirmed') Đã xác nhận @break
                        @case('processing') Đang xử lý @break
                        @case('shipped') Đã giao @break
                        @case('completed') Hoàn thành @break
                        @case('cancelled') Đã hủy @break
                        @default {{ $order->status }}
                    @endswitch
                </span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="customer-section">
            <div class="section-title">THÔNG TIN KHÁCH HÀNG</div>
            <div class="customer-info">
                <div class="info-row">
                    <span class="info-label">Họ tên:</span>
                    {{ $order->customer_name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $order->customer_email }}
                </div>
                <div class="info-row">
                    <span class="info-label">Điện thoại:</span>
                    {{ $order->customer_phone }}
                </div>
                <div class="info-row">
                    <span class="info-label">Địa chỉ:</span>
                    {{ $order->full_address }}
                </div>
                @if($order->notes)
                <div class="info-row">
                    <span class="info-label">Ghi chú:</span>
                    {{ $order->notes }}
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="section-title">CHI TIẾT ĐơN HÀNG</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 45%">Sản phẩm</th>
                    <th style="width: 15%" class="text-center">Số lượng</th>
                    <th style="width: 17.5%" class="text-right">Đơn giá</th>
                    <th style="width: 17.5%" class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product && $item->product->sku)
                            <br><small>SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', '.') }}₫</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Tạm tính:</td>
                    <td class="total-value">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td class="total-label">Giảm giá:</td>
                    <td class="total-value">-{{ number_format($order->discount, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                <tr>
                    <td class="total-label">Phí vận chuyển:</td>
                    <td class="total-value">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label">TỔNG CỘNG:</td>
                    <td class="total-value">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        <div style="clear: both;">
            <div class="section-title">THÔNG TIN THANH TOÁN</div>
            <div class="customer-info">
                <div class="info-row">
                    <span class="info-label">Phương thức:</span>
                    {{ $order->payment_method }}
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    @switch($order->payment_status)
                        @case('pending') Chờ thanh toán @break
                        @case('paid') Đã thanh toán @break
                        @case('failed') Thất bại @break
                        @case('refunded') Đã hoàn tiền @break
                        @default {{ $order->payment_status }}
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            Cảm ơn quý khách đã tin tương và sử dụng dịch vụ của chúng tôi!
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p><strong>Lưu ý:</strong> Hóa đơn này được tạo tự động từ hệ thống.</p>
            <p><strong>Thời gian xuất:</strong> {{ $generated_at }}</p>
            <p><strong>Hotline hỗ trợ:</strong> {{ $settings->hotline ?? 'N/A' }}</p>
        </div>
    </div>
</body>
</html>
