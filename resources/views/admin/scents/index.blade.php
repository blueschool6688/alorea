@extends('admin.layout')

@section('title', 'Quản lý mùi hương')
@section('page-title', 'Danh sách mùi hương')

@push('styles')
<style>
    .scent-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        display: inline-block;
        vertical-align: middle;
    }
    .intensity-bar {
        width: 100px;
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        position: relative;
        overflow: hidden;
    }
    .intensity-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745 0%, #ffc107 50%, #dc3545 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    .type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-weight: 500;
    }
    .type-top { background: #e3f2fd; color: #1976d2; }
    .type-middle { background: #f3e5f5; color: #7b1fa2; }
    .type-base { background: #fff3e0; color: #f57c00; }

    .category-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .bulk-actions {
        display: none;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .bulk-actions.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Quản lý mùi hương</h4>
        <p class="text-muted mb-0">Danh sách {{ $scents->total() }} mùi hương</p>
    </div>
    <a href="{{ route('admin.scents.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Thêm mùi hương
    </a>
</div>

<!-- Filter Card -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.scents.index') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">Tất cả loại</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-outline-primary w-100">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($scents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Mùi hương</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scents as $scent)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $scent->id }}"
                                           class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <h6 class="mb-0">{{ $scent->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($scent->description, 50) }}</small>
                                        </div>
                                        @if($scent->is_popular)
                                            <span class="badge bg-warning ms-2">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="">
                                        {{ $scent->type_name }}
                                    </span>
                                </td>
                                <td>
                                    @if($scent->is_active)
                                        <span class="">Hoạt động</span>
                                    @else
                                        <span class="">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.scents.edit', $scent) }}"
                                           class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" title="Thêm">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form method="POST" action="{{ route('admin.scents.toggle-status', $scent) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-power-off me-2"></i>
                                                            {{ $scent->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.scents.destroy', $scent) }}"
                                                          class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa mùi hương này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i>Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Hiển thị {{ $scents->firstItem() }} - {{ $scents->lastItem() }} của {{ $scents->total() }} kết quả
                </div>
                {{ $scents->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-wind fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không có mùi hương nào</h5>
                <p class="text-muted">Thêm mùi hương đầu tiên để bắt đầu</p>
                <a href="{{ route('admin.scents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm mùi hương
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Bulk selection functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedCheckboxes.length > 0) {
        bulkActions.classList.add('show');
        selectedCount.textContent = selectedCheckboxes.length;

        // Add selected IDs to bulk form
        const bulkForm = document.getElementById('bulkForm');
        // Remove existing hidden inputs
        bulkForm.querySelectorAll('input[name="selected_ids[]"]').forEach(input => input.remove());

        // Add new hidden inputs
        selectedCheckboxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selected_ids[]';
            hiddenInput.value = checkbox.value;
            bulkForm.appendChild(hiddenInput);
        });
    } else {
        bulkActions.classList.remove('show');
    }

    // Update select all checkbox state
    const selectAll = document.getElementById('selectAll');
    const totalCheckboxes = document.querySelectorAll('.row-checkbox').length;
    if (selectedCheckboxes.length === totalCheckboxes && totalCheckboxes > 0) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else if (selectedCheckboxes.length > 0) {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    updateBulkActions();
}

// Initialize on page load
updateBulkActions();
</script>
@endpush
