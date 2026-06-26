@extends('layouts.app')

@section('title', 'Chỉnh sửa Cấu hình phê duyệt')

@section('content')
<div class="card shadow-sm" style="max-width: 550px;">
    <div class="card-header bg-white"><i class="fa-solid fa-user-shield me-1"></i> Sửa thiết lập luồng</div>
    <div class="card-body">
        <form action="{{ route('admin.approvers.update', $approver->id) }}" method="POST">
            @csrf
            <div class="mb-3">
    <label class="form-label">Mã Phòng ban (DepartmentCode)</label>
    <select name="DepartmentCode" class="form-select" required>
        @foreach($departments as $dept)
            <option value="{{ $dept }}" {{ $approver->DepartmentCode == $dept ? 'selected' : '' }}>{{ $dept }}</option>
        @endforeach
    </select>
</div>
            
            <div class="mb-3">
                <label class="form-label">Cấp độ nhân viên (Level)</label>
                <input type="number" name="level" class="form-control" value="{{ old('level', $approver->level) }}" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Người phê duyệt được chỉ định</label>
                <select name="employeeID" id="employeeSelect" class="form-select" required>
                    <option value="">-- Chọn người phê duyệt --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $approver->employeeID == $u->id ? 'selected' : '' }}>
                            {{ $u->full_name }} ({{ $u->DepartmentCode ?? 'No Dept' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Cập nhật</button>
            <a href="{{ route('admin.approvers.index') }}" class="btn btn-light ms-2">Hủy bỏ</a>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#employeeSelect').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Chọn người phê duyệt --",
            allowClear: true
        });
    });
</script>
@endsection