@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-body p-4">
            <h4 class="text-center mb-4"><i class="fa-solid fa-building text-primary"></i> Đăng nhập hệ thống</h4>
            
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
        </div>
    </div>
</div>
@endsection