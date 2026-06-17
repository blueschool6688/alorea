{{-- filepath: resources/views/admin/flash-sales/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Flash Sales Management')
@section('page-title', 'Quản lý Flash Sale')

@push('styles')
<style>
    .flash-sale-banner {
        width: 60px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .table-actions {
        white-space: nowrap;
    }
    .table-actions .btn {
        padding: 0.25rem 0.5rem;
        margin: 0 0.125rem;
    }
    .filters-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .time-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }
    .time-status.upcoming { background: #e3f2fd; color: #1565c0; }
    .time-status.running { background: #e8f5e8; color: #2e7d32; }
    .time-status.expired { background: #ffebee; color: #c62828; }
    .progress-mini {
        width: 60px;
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }
    .progress-mini .progress-bar {
        height: 100%;
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quản lý Flash Sale</h4>
        <p class="text-muted mb-0">Danh sách tất cả chương trình flash sale</p>
    </div>
    <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-dark">
        <i class="fas fa-plus me-2"></i>Tạo Flash Sale
    </a>
</div>

<!-- Filters -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.flash-sales.index') }}">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control"
                       placeholder="Tìm kiếm..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang chạy</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                    <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="time_status" class="form-select">
                    <option value="">Tất cả thời gian</option>
                    <option value="upcoming" {{ request('time_status') === 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="running" {{ request('time_status') === 'running' ? 'selected' : '' }}>Đang chạy</option>
                    <option value="expired" {{ request('time_status') === 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                </select>
            </div>
             <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Flash Sales Table -->
<div class="card">
    <div class="card-body">
        @if($flashSales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Banner</th>
                            <th>Thông tin</th>
                            <th>Thời gian</th>
                            <th>Giảm giá</th>
                            {{-- <th>Sản phẩm</th> --}}
                            <th>Tiến độ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flashSales as $flashSale)
                            <tr>
                                <td>
                                    @if($flashSale->banner_url)
                                        <img width="200" height="200" src="{{ $flashSale->banner_url }}" alt="{{ $flashSale->name }}" class="flash-sale-banner object-fit-contain">
                                    @else
                                        <div class="flash-sale-banner bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif

                                    @if($flashSale->is_featured)
                                        <i class="fas fa-star text-warning" title="Nổi bật"></i>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $flashSale->name }}</strong>
                                        @if($flashSale->description)
                                            <br><small class="text-muted">{{ Str::limit($flashSale->description, 50) }}</small>
                                        @endif
                                        <br><code class="small">{{ $flashSale->slug }}</code>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><strong>Bắt đầu:</strong> {{ $flashSale->start_time->format('d/m/Y H:i') }}</div>
                                        <div><strong>Kết thúc:</strong> {{ $flashSale->end_time?->format('d/m/Y H:i') }}</div>
                                        <span class="time-status {{ $flashSale->time_status }}">
                                            {{ ucfirst($flashSale->time_status) }}
                                        </span>
                                        @if($flashSale->time_status)
                                            <div class="text-muted">{{ $flashSale->time_status }}</div>
                                        @endif
                                    </div>
                                </td>
                       <td>
    <div class="small">
        @if($flashSale->discount_type === 'percent')
            <strong class="text-danger">{{ $flashSale->discount_value }}%</strong>
            <br><span class="text-muted">Giảm theo %</span>
        @elseif($flashSale->discount_type === 'fixed')
            <strong class="text-success">{{ number_format($flashSale->discount_value, 0, ',', '.') }}đ</strong>
            <br><span class="text-muted">Giảm cố định</span>
        @else
            <span class="text-muted">Chưa cài đặt</span>
        @endif

        @if($flashSale->max_discount_amount && $flashSale->discount_type === 'percent')
            <br><small class="text-info">Tối đa: {{ number_format($flashSale->max_discount_amount, 0, ',', '.') }}đ</small>
        @endif
    </div>
</td>
                                {{-- <td>
                                    <span class="badge bg-primary">
                                        {{ $flashSale->type_all ? 'Tất cả' :  ($flashSale->products_count ?? $flashSale->products->count()) }} sản phẩm
                                    </span>
                                </td> --}}
                                <td>
                                    @if($flashSale->max_quantity)
                                        <div class="small">
                                            {{ $flashSale->used_quantity }}/{{ $flashSale->max_quantity }}
                                        </div>
                                        {{-- <div class="progress-mini">
                                            <div class="progress-bar bg-primary" style="width: {{ $flashSale->progress_percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($flashSale->progress_percentage, 1) }}%</small> --}}
                                    @else
                                        <small class="text-muted">Không giới hạn</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $flashSale->status_badge }}">
                                        {{ $flashSale->status_text }}
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.flash-sales.edit', $flashSale) }}"
                                           class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(in_array($flashSale->status, ['active', 'paused']))
                                            <form method="POST" action="{{ route('admin.flash-sales.toggle-status', $flashSale) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                        title="{{ $flashSale->status === 'active' ? 'Tạm dừng' : 'Kích hoạt' }}">
                                                    <i class="fas fa-{{ $flashSale->status === 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteFlashSale({{ $flashSale->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị {{ $flashSales->firstItem() }} - {{ $flashSales->lastItem() }}
                    trong tổng số {{ $flashSales->total() }} kết quả
                </div>
                {{ $flashSales->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bolt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy flash sale nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo flash sale mới!</p>
                <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Tạo flash sale đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa flash sale này không?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hành động này sẽ xóa flash sale và tất cả liên kết với sản phẩm!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa flash sale</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteFlashSale(flashSaleId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/flash-sales/${flashSaleId}`;
    modal.show();
}

// Auto refresh page every 60 seconds to update time status
setTimeout(function() {
    location.reload();
}, 60000);
</script>
@endpush
