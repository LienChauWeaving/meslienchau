@extends('layouts.app')

@section('title', 'Tất cả đơn nghỉ phép')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom pb-3 pt-3">
        <form method="GET" action="{{ route('leaves.all') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã đơn, Tên, Username..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Phòng ban</label>
                <select name="department" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Đang chờ duyệt</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Từ chối</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                <a href="{{ route('leaves.all') }}" class="btn btn-light"><i class="fa-solid fa-rotate-right"></i> Reset</a>
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Người tạo</th>
                        <th>Loại phép</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Xác nhận</th>
                        <th>Ngày tạo</th>
                        <th class="text-center pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaves as $leave)
                    <tr>
                        <td class="ps-4 fw-medium text-primary">{{ $leave->id }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $leave->user->full_name ?? 'N/A' }}</span>
                                <span class="text-muted small">{{ $leave->user->username ?? 'N/A' }} - {{ $leave->user->DepartmentCode ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>{{ $leave->leave_type }}</td>
                        <td>
                            <div class="small">
                                <i class="fa-regular fa-calendar text-muted me-1"></i> {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y H:i') }}<br>
                                <i class="fa-solid fa-arrow-right text-muted mx-1"></i> {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td>
                            @if($leave->status == 'Pending')
                                <span class="badge bg-warning text-dark"><i class="fa-regular fa-clock me-1"></i> Chờ duyệt</span>
                            @elseif($leave->status == 'Approved')
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Đã duyệt</span>
                            @elseif($leave->status == 'Rejected')
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> Từ chối</span>
                            @elseif($leave->status == 'Cancelled')
                                <span class="badge bg-secondary"><i class="fa-solid fa-ban me-1"></i> Đã huỷ</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $confirmStatusToCheck = $leave->status == 'Cancelled' ? 'Confirmed_Cancel' : 'Confirmed';
                                $hasConfirmed = collect($leave->approvalLogs)->contains('Status', $confirmStatusToCheck);
                            @endphp
                            @if($hasConfirmed)
                                <span class="badge bg-primary"><i class="fa-solid fa-check"></i> Đã xác nhận</span>
                            @else
                                <span class="badge bg-light text-secondary border"><i class="fa-regular fa-circle-xmark"></i> Chưa</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $leave->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('leaves.workflow', ['id' => $leave->id, 'source' => 'all']) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fs-2 mb-2 text-light"></i><br>
                            Không tìm thấy đơn nghỉ phép nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($leaves->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $leaves->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
