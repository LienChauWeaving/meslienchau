@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm border-0" style="width: 400px;">
        <div class="card-header bg-dark text-white text-center py-3">
            <h5 class="mb-0"><i class="fa-solid fa-user-shield me-2"></i> QUẢN TRỊ HỆ THỐNG</h5>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tài khoản Admin</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">Đăng nhập Quản trị</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại cổng Nhân viên</a>
            </div>
        </div>
    </div>
</div>
@endsection