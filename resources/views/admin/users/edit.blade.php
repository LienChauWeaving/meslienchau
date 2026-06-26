@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin Người dùng')

@section('content')
<div class="card shadow-sm" style="max-width: 650px;">
    <div class="card-header bg-white"><i class="fa-solid fa-user-gear me-1"></i> Form cập nhật: {{ $user->username }}</div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Họ và tên nhân viên</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mã Phòng ban (DepartmentCode)</label>
                    <input type="text" name="DepartmentCode" class="form-control" value="{{ old('DepartmentCode', $user->DepartmentCode) }}" placeholder="Ví dụ: IT, HR, SALE">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tên Phòng ban</label>
                    <input type="text" name="department_name" class="form-control" value="{{ old('department_name', $user->department_name) }}" placeholder="Ví dụ: Phòng IT, Hành chính">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chức danh</label>
                    <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $user->job_title) }}" placeholder="Ví dụ: Trưởng phòng, Nhân viên">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Trạng thái tài khoản</label>
                <select name="status" class="form-select">
                    <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Khóa tài khoản</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Mật khẩu mới (Để trống nếu không muốn đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới...">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light ms-2">Quay lại</a>
        </form>
    </div>
</div>
@endsection