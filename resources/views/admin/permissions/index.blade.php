@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="mb-0 text-primary">
                <i class="fa-solid fa-users-gear me-2"></i>Phân quyền hệ thống
            </h2>
            <p class="text-muted mt-1 mb-0">Quản lý và cấp phát quyền hạn cho nhân sự trong hệ thống.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Cấp quyền nhân sự</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="user_id" class="form-label fw-semibold text-secondary">Chọn nhân viên <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select form-select-lg" required>
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->username }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary">Chọn quyền <span class="text-danger">*</span></label>
                            <div class="card border border-light bg-light">
                                <div class="card-body py-2 px-3">
                                    <div class="form-check py-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="HR_LEAVES" id="perm_hr" checked>
                                        <label class="form-check-label fw-medium ms-2 cursor-pointer" for="perm_hr">
                                            Quyền HR (Xem và xác nhận đơn nghỉ phép toàn hệ thống)
                                        </label>
                                    </div>
                                    <!-- Các quyền khác sẽ được thêm vào đây sau -->
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu phân quyền
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list-ul text-primary me-2"></i>Danh sách nhân sự đã phân quyền</h5>
                    <span class="badge bg-primary rounded-pill fs-6">{{ $userPermissions->count() }} người</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th class="ps-4">Tài khoản</th>
                                    <th>Họ tên</th>
                                    <th>Các quyền</th>
                                    <th class="text-center pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userPermissions as $userId => $permissions)
                                    @php
                                        $user = $permissions->first()->user;
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-medium text-dark">{{ $user->username }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle me-3 d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold text-dark">{{ $user->full_name }}</span>
                                                    @if($user->job_title)
                                                        <div class="text-muted small">{{ $user->job_title }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @foreach($permissions as $perm)
                                                @if($perm->permission == 'HR_LEAVES')
                                                    <span class="badge bg-info text-dark shadow-sm my-1"><i class="fa-solid fa-user-tie me-1"></i> HR</span>
                                                @else
                                                    <span class="badge bg-secondary shadow-sm my-1">{{ $perm->permission }}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center pe-4">
                                            <form action="{{ route('admin.permissions.destroy', $user->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn gỡ tất cả quyền của nhân viên này?');" title="Gỡ tất cả quyền">
                                                    <i class="fa-solid fa-user-minus me-1"></i> Gỡ quyền
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <div class="mb-3">
                                                <i class="fa-solid fa-users-slash fs-1 text-light-gray"></i>
                                            </div>
                                            <h6 class="mb-1 text-secondary">Chưa có dữ liệu</h6>
                                            <p class="small mb-0">Hiện tại chưa có nhân sự nào được phân quyền hệ thống.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-light-gray { color: #e9ecef; }
    .avatar { border: 2px solid #fff; }
    .table th { font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    .btn-outline-danger:hover { background-color: #dc3545; color: white; }
    .cursor-pointer { cursor: pointer; }
    /* Fix Select2 height to match form-control-lg if needed */
    .select2-container--bootstrap-5 .select2-selection { min-height: calc(1.5em + 1rem + 2px); padding: 0.5rem 1rem; font-size: 1.25rem; border-radius: 0.3rem; }
</style>

@section('scripts')
<script>
    $(document).ready(function() {
        $('#user_id').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Tìm kiếm nhân viên --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
@endsection
