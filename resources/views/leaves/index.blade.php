@extends('layouts.app')

@section('title', 'Quản lý nghỉ phép')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-list me-1"></i> Danh sách đơn từ</span>
        <a href="{{ route('leaves.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Tạo đơn mới</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
    <tr>
        <th>Mã đơn</th>
        <th>Từ ngày</th>
        <th>Đến ngày</th>
        <th>Lý do</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
</thead>
            <tbody>
                @forelse($leaves as $leave)
              <tr style="cursor: pointer;" onclick="window.location='{{ route('leaves.workflow', $leave->id) }}'" class="align-middle">
                <td>
                    {{ $leave->id }}
                    @if($leave->is_cancellation)
                        <br><span class="badge bg-danger mt-1" style="font-size:0.7rem;"><i class="fa-solid fa-ban"></i> Xin Hủy</span>
                    @endif
                </td>
                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('H:i d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('H:i d/m/Y') }}</td>
                    <td>{{ $leave->reason }}</td>
                    <td>
                        <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Rejected' ? 'danger' : ($leave->status == 'Cancelled' ? 'dark' : 'warning text-dark')) }}">
                            {{ $leave->status == 'Cancelled' ? 'Đã hủy' : $leave->status }}
                        </span>
                    </td>
                    <td>{{ $leave->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">Chưa có đơn xin nghỉ phép nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection