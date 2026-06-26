@extends('layouts.app')

@section('title', 'Quản lý danh sách Người dùng')

@section('content')

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-users me-1"></i> Bảng dữ liệu Users</span>
        <div>
            <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fa-solid fa-file-excel"></i> Nhập từ Excel
            </button>
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Thêm nhân viên mới</a>
        </div>
    </div>
    <div class="card-body p-3 border-bottom bg-light">
        <form id="searchForm" method="GET" action="{{ route('admin.users.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">-- Tất cả bộ phận --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->DepartmentCode }}" {{ request('department') == $dept->DepartmentCode ? 'selected' : '' }}>
                            {{ $dept->department_name ? $dept->department_name . ' (' . $dept->DepartmentCode . ')' : $dept->DepartmentCode }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Tìm mã nhân viên hoặc tên..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100"><i class="fa-solid fa-rotate-left"></i> Đặt lại</a>
            </div>
        </form>
    </div>
    <div id="usersTableContainer">
        @include('admin.users.table')
    </div>
</div>

<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-file-excel text-success me-1"></i> Nhập danh sách nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tải tệp Excel lên (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <div class="text-end mt-2">
                        <a href="{{ route('admin.users.template') }}" class="text-decoration-none text-success small">
                            <i class="fa-solid fa-download me-1"></i> Tải file mẫu (Template)
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-upload me-1"></i> Cập nhật dữ liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let typingTimer;
        const doneTypingInterval = 500; // 500ms
        const $input = $('#searchInput');
        const $form = $('#searchForm');
        const $container = $('#usersTableContainer');

        function fetchResults(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $container.html(data);
                },
                error: function() {
                    console.error('Lỗi khi tải dữ liệu');
                }
            });
        }

        $form.on('submit', function(e) {
            e.preventDefault();
            fetchResults($(this).attr('action') + '?' + $(this).serialize());
        });

        // Trigger on dropdown change
        $('select[name="department"]').on('change', function() {
            $form.trigger('submit');
        });

        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                $form.trigger('submit');
            }, doneTypingInterval);
        });

        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchResults($(this).attr('href'));
        });
    });
</script>
@endsection