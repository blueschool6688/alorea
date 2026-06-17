{{-- filepath: c:\laragon\www\perfume-client\resources\views\admin\contacts\show.blade.php --}}
@extends('admin.layout')

@section('title', 'Chi tiết liên hệ')
@section('page-title', 'Chi tiết liên hệ')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Contact Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-envelope mr-2"></i>Thông tin liên hệ
                </h6>
                <div>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Quay lại
                    </a>
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                data-toggle="dropdown">
                            Cập nhật trạng thái
                        </button>
                        <div class="dropdown-menu">
                            <form method="POST" action="{{ route('admin.contacts.update-status', $contact) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="status" value="pending" class="dropdown-item">
                                    <i class="fas fa-clock text-warning mr-2"></i>Chờ xử lý
                                </button>
                                <button type="submit" name="status" value="read" class="dropdown-item">
                                    <i class="fas fa-eye text-info mr-2"></i>Đã đọc
                                </button>
                                <button type="submit" name="status" value="replied" class="dropdown-item">
                                    <i class="fas fa-reply text-success mr-2"></i>Đã trả lời
                                </button>
                                <button type="submit" name="status" value="closed" class="dropdown-item">
                                    <i class="fas fa-check text-secondary mr-2"></i>Đã đóng
                                </button>
                            </form>
                        </div>
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

                <!-- Contact Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Thông tin người gửi:</h6>
                        <p class="mb-1"><strong>Tên:</strong> {{ $contact->name }}</p>
                        <p class="mb-1"><strong>Email:</strong>
                            <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                        </p>
                        @if($contact->phone)
                            <p class="mb-1"><strong>Điện thoại:</strong>
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Thông tin liên hệ:</h6>
                        <p class="mb-1"><strong>Ngày gửi:</strong> {{ $contact->formatted_created_at }}</p>
                        <p class="mb-1"><strong>Trạng thái:</strong>
                            <span class="badge badge-{{ $contact->status_color }}">
                                {{ $contact->status_label }}
                            </span>
                        </p>
                        @if($contact->replied_by)
                            <p class="mb-1"><strong>Người trả lời:</strong> {{ $contact->repliedBy->name }}</p>
                            <p class="mb-1"><strong>Thời gian trả lời:</strong> {{ $contact->replied_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Subject -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-primary">Chủ đề:</h6>
                    <p class="h5">{{ $contact->subject }}</p>
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-primary">Nội dung:</h6>
                    <div class="border p-3 bg-light rounded">
                        {!! nl2br(e($contact->message)) !!}
                    </div>
                </div>

                <!-- Admin Reply -->
                @if($contact->admin_reply)
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-success">Phản hồi của admin:</h6>
                        <div class="border p-3 bg-success-light rounded">
                            {!! nl2br(e($contact->admin_reply)) !!}
                        </div>
                        <small class="text-muted">
                            Trả lời bởi {{ $contact->repliedBy->name ?? 'Admin' }}
                            lúc {{ $contact->replied_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Reply Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-reply mr-2"></i>Phản hồi
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.contacts.reply', $contact) }}">
                    @csrf
                    <div class="form-group">
                        <label for="admin_reply">Nội dung phản hồi:</label>
                        <textarea name="admin_reply" id="admin_reply" class="form-control" rows="8"
                                  placeholder="Nhập phản hồi của bạn...">{{ old('admin_reply', $contact->admin_reply) }}</textarea>
                        @error('admin_reply')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-paper-plane mr-2"></i>
                            {{ $contact->admin_reply ? 'Cập nhật phản hồi' : 'Gửi phản hồi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Thao tác nhanh
                </h6>
            </div>
            <div class="card-body">
                <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}"
                   class="btn btn-info btn-block btn-sm mb-2">
                    <i class="fas fa-envelope mr-2"></i>Gửi email trực tiếp
                </a>

                @if($contact->phone)
                    <a href="tel:{{ $contact->phone }}" class="btn btn-warning btn-block btn-sm mb-2">
                        <i class="fas fa-phone mr-2"></i>Gọi điện
                    </a>
                @endif

                <button type="button" class="btn btn-danger btn-block btn-sm"
                        onclick="deleteContact({{ $contact->id }})">
                    <i class="fas fa-trash mr-2"></i>Xóa liên hệ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function deleteContact(id) {
    if (confirm('Bạn có chắc chắn muốn xóa liên hệ này?')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.bg-success-light {
    background-color: #d4edda !important;
}
</style>
@endpush
