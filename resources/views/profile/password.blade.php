@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-key me-2"></i> Đổi mật khẩu</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="4">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required minlength="4">
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
