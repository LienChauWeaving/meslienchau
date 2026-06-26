@extends('layouts.app')

@section('title', 'Thêm Nhân viên mới')

@section('content')
<div class="card shadow-sm" style="max-width: 650px;">
    <div class="card-header bg-white"><i class="fa-solid fa-user-plus me-1"></i> Nhập thông tin nhân viên</div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Họ và tên nhân viên</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mã Phòng ban (DepartmentCode)</label>
                    <input type="text" name="DepartmentCode" class="form-control" value="{{ old('DepartmentCode') }}" placeholder="Ví dụ: IT, HR, SALE">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tên Phòng ban</label>
                    <input type="text" name="department_name" class="form-control" value="{{ old('department_name') }}" placeholder="Ví dụ: Phòng IT, Hành chính">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chức danh</label>
                    <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}" placeholder="Ví dụ: Trưởng phòng, Nhân viên">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Trạng thái tài khoản</label>
                <select name="status" class="form-select">
                    <option value="1" selected>Kích hoạt</option>
                    <option value="0">Khóa tài khoản</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm mới</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light ms-2">Quay lại</a>
        </form>
    </div>
</div>
@endsection