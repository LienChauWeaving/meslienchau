@extends('layouts.app')

@section('title', 'Cấu hình Danh sách Người phê duyệt')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><i class="fa-solid fa-plus me-1"></i> Thiết lập luồng mới</div>
            <div class="card-body">
                <form action="{{ route('admin.approvers.store') }}" method="POST">
                    @csrf
                   <div class="mb-3">
    <label class="form-label">Mã Phòng ban (DepartmentCode)</label>
    <select name="DepartmentCode" class="form-select" required>
        <option value="">-- Chọn phòng ban --</option>
        @foreach($departments as $dept)
            <option value="{{ $dept }}">{{ $dept }}</option>
        @endforeach
    </select>
</div>
                    <div class="mb-3">
                        <label class="form-label">Áp dụng cho nhân viên Level</label>
                        <input type="number" name="level" class="form-control" placeholder="Ví dụ: 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Người phê duyệt được chỉ định</label>
                        <select name="employeeID" id="employeeSelect" class="form-select" required>
                            <option value="">-- Chọn người phê duyệt --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->DepartmentCode ?? 'No Dept' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-square-plus me-1"></i> Thêm cấu hình</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><i class="fa-solid fa-list me-1"></i> Danh sách cấu hình hiện tại</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Phòng ban</th>
                            <th>Cấp độ nhân viên</th>
                            <th>Người phê duyệt</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvers as $app)
                        <tr>
                            <td><span class="badge bg-primary">{{ $app->DepartmentCode }}</span></td>
                            <td>Nhân viên cấp {{ $app->level }}</td>
                            <td><strong>{{ $app->employee->full_name ?? 'N/A' }}</strong></td>
                            <td class="text-center">
                                <a href="{{ route('admin.approvers.edit', $app->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.approvers.delete', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa cấu hình này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Chưa có cấu hình phê duyệt nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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