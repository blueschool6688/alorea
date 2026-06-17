{{-- filepath: c:\laragon\www\perfume-client\resources\views\admin\contacts\index.blade.php --}}
@extends('admin.layout')

@section('title', 'Quản lý liên hệ')
@section('page-title', 'Quản lý liên hệ')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng cộng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ xử lý</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đã đọc</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['read']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã trả lời</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['replied']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-reply fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Đã đóng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['closed']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-white-50 small">Hôm nay</div>
                        <div class="h4 mb-0">{{ number_format($stats['today']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-envelope mr-2"></i>Danh sách liên hệ
                </h6>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex">
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã trả lời</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Tìm kiếm..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm ml-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif


        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="50"><input type="checkbox" id="selectAllTable"></th>
                        <th>Thông tin liên hệ</th>
                        <th>Chủ đề</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr class="{{ $contact->status === 'pending' ? 'table-warning' : '' }}">
                            <td>
                                <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}"
                                       form="bulkForm" class="contact-checkbox">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $contact->name }}</div>
                                        <div class="small text-gray-500">{{ $contact->email }}</div>
                                        @if($contact->phone)
                                            <div class="small text-gray-500">{{ $contact->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ Str::limit($contact->subject, 50) }}</span>
                            </td>
                            <td>
                                <div class="text-wrap" style="max-width: 300px;">
                                    {{ Str::limit($contact->message, 100) }}
                                </div>
                            </td>
                            <td>
                                 {{ $contact->status_label }}
                                @if($contact->replied_at)
                                    <div class="small text-muted mt-1">
                                        Trả lời: {{ $contact->replied_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $contact->formatted_created_at }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.contacts.show', $contact) }}"
                                       class="btn btn-info btn-sm" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Xóa"
                                            onclick="deleteContact({{ $contact->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <div>Chưa có liên hệ nào</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $contacts->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Delete contact
function deleteContact(id) {
    if (confirm('Bạn có chắc chắn muốn xóa liên hệ này?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/contacts/${id}`;
        form.submit();
    }
}
</script>
@endpush
