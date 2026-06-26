@extends('layouts.app')

@section('title', 'Cài đặt Uỷ quyền Phê duyệt')

@section('content')
<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-user-clock me-1 text-primary"></i> Chọn người duyệt thay
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info me-1"></i>
            Khi bạn chọn người thay thế, người đó sẽ có quyền duyệt toàn bộ các đơn đang chờ bạn duyệt. Hệ thống sẽ lưu vết người đó ký thay cho bạn.
        </div>

        <form action="{{ route('profile.delegate.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label">Người được uỷ quyền</label>
                <select name="delegate_id" id="delegateSelect" class="form-select">
                    <option value="">-- Không có (Tự duyệt) --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $user->delegate_id == $u->id ? 'selected' : '' }}>
                            {{ $u->full_name }} ({{ $u->department_name ?? $u->DepartmentCode ?? 'Chưa cấu hình phòng ban' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Lưu thiết lập</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#delegateSelect').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Không có (Tự duyệt) --",
            allowClear: true
        });
    });
</script>
@endsection
