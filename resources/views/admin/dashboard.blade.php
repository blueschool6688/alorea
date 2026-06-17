{{-- filepath: c:\laragon\www\perfume-client\resources\views\admin\dashboard.blade.php --}}
@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard - Tổng quan hệ thống')

@section('content')
<!-- Main Stats Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng khách hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_users']) }}
                        </div>
                        @if(isset($stats['users_growth']))
                            <div class="mt-2 d-flex align-items-center">
                                <i class="fas fa-arrow-{{ $stats['users_growth'] >= 0 ? 'up text-success' : 'down text-danger' }} mr-1"></i>
                                <span class="text-{{ $stats['users_growth'] >= 0 ? 'success' : 'danger' }} text-xs">
                                    {{ abs($stats['users_growth']) }}% tháng này
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tổng sản phẩm
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_products']) }}
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-check-circle text-success"></i>
                                {{ $stats['active_products'] ?? 0 }} đang bán
                            </small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tổng đơn hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_orders']) }}
                        </div>
                        <div class="mt-2">
                            <small class="text-warning">
                                <i class="fas fa-clock"></i>
                                {{ $stats['pending_orders'] ?? 0 }} chờ xử lý
                            </small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tổng doanh thu
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_revenue']) }} VNĐ
                        </div>
                        @if(isset($stats['revenue_growth']))
                            <div class="mt-2">
                                <small class="text-{{ $stats['revenue_growth'] >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($stats['revenue_growth']) }}% tháng này
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div class="row mb-4">
    <!-- Today's Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-white-50 small">Đơn hàng hôm nay</div>
                        <div class="h4 mb-0">{{ $stats['today_orders'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-white-50 small">Doanh thu hôm nay</div>
                        <div class="h4 mb-0">{{ number_format($stats['today_revenue'] ?? 0) }} VNĐ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-white-50 small">Giá trị đơn hàng TB</div>
                        <div class="h4 mb-0">{{ number_format($stats['avg_order_value'] ?? 0) }} VNĐ</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-white-50 small">Sản phẩm sắp hết</div>
                        <div class="h4 mb-0">{{ $stats['low_stock_products'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Sales Chart -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Biểu đồ doanh thu 12 tháng
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Xuất báo cáo:</div>
                        <a class="dropdown-item" href="#">PDF</a>
                        <a class="dropdown-item" href="#">Excel</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status & Payment Chart -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Trạng thái đơn hàng
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Hoàn thành
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Chờ xử lý
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> Đã hủy
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock mr-2"></i>Đơn hàng gần đây
                </h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye mr-1"></i>Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                                        @php
                                            $statusLabel = [
                                                'pending' => 'Đang chờ',
                                                'confirmed' => 'Đã xác nhận',
                                                'cancelled' => 'Đã hủy',
                                                'processing' => 'Đang xử lý',
                                                'completed' => 'Hoàn thành',
                                            ];
                                            $paymentLabels = [
                                                'pending' => 'Chờ thanh toán',
                                                'paid' => 'Đã thanh toán',
                                                'failed' => 'Thất bại'
                                            ];
                                        @endphp
                                <tr>
                                    <td class="font-weight-bold">#{{ $order->order_number ?? $order->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-bold">
                                                    {{ $order->user->name ?? $order->customer_name ?? 'Khách vãng lai' }}
                                                </div>
                                                <div class="small text-gray-500">
                                                    {{ $order->user->email ?? $order->customer_email ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-weight-bold">{{ number_format($order->total) }} VNĐ</td>
                                    <td>
                                        <span class="">
                                            {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>

                                        <span >
                                            {{ $paymentLabels[$order->payment_status] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-success btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                        <div>Chưa có đơn hàng nào</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products & Quick Actions -->
    <div class="col-lg-4">
        <!-- Top Products -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star mr-2"></i>Sản phẩm bán chạy
                </h6>
            </div>
            <div class="card-body">
                @forelse($top_products as $index => $product)
                    <div class="d-flex align-items-center mb-3 p-2 {{ $index < 3 ? 'border-left border-' . ['primary', 'success', 'warning'][$index] : '' }}">
                        <div class="me-3 position-relative">
                            <img src="{{ $product->main_image_url }}" class="rounded" width="50" height="50"
                                 style="object-fit: cover;">
                            @if($index < 3)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-{{ ['primary', 'success', 'warning'][$index] }}">
                                    {{ $index + 1 }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold mb-1">{{ Str::limit($product->name, 25) }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-success">
                                    <i class="fas fa-shopping-cart"></i> {{ $product->order_items_count }} đã bán
                                </small>
                                <div class="text-right">
                                    <div class="font-weight-bold text-primary">{{ number_format($product->price) }} VNĐ</div>
                                    @if($product->stock <= 10)
                                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Sắp hết</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                        <div>Chưa có sản phẩm nào</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if(($stats['low_stock_products'] ?? 0) > 0)
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Cảnh báo!</strong> Có {{ $stats['low_stock_products'] }} sản phẩm sắp hết hàng.
            <a href="#" class="alert-link">Xem ngay</a>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }

.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-area {
    position: relative;
    height: 400px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart with real data
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthly_sales as $sale)
                '{{ $sale->month_name }}',
            @endforeach
        ],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: [
                @foreach($monthly_sales as $sale)
                    {{ $sale->total }},
                @endforeach
            ],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#4e73df',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                callbacks: {
                    label: function(context) {
                        return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                    }
                }
            }
        }
    }
});

// Status Chart with real data
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Hoàn thành', 'Chờ xử lý', 'Đang xử lý', 'Đã hủy'],
        datasets: [{
            data: [
                {{ $stats['completed_orders'] ?? 0 }},
                {{ $stats['pending_orders'] ?? 0 }},
                {{ $stats['processing_orders'] ?? 0 }},
                {{ $stats['cancelled_orders'] ?? 0 }}
            ],
            backgroundColor: ['#1cc88a', '#f6c23e', '#4e73df', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#dda20a', '#2653d4', '#be2617'],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        },
        cutout: '60%'
    }
});

</script>
@endpush
