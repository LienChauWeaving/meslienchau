<div class="card-body p-0 table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>Mã bộ phận</th>
                <th>Tên bộ phận</th>
                <th>Chức danh</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td><code>{{ $user->username }}</code></td>
                <td><strong>{{ $user->full_name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td><span class="badge bg-secondary">{{ $user->DepartmentCode ?? 'Chưa gán' }}</span></td>
                <td>{{ $user->department_name }}</td>
                <td>{{ $user->job_title }}</td>
                <td>
                    <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                        {{ $user->status ? 'Đang hoạt động' : 'Khóa' }}
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Không tìm thấy người dùng nào phù hợp.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($users->hasPages())
<div class="card-footer bg-white pt-3 pb-0">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
@endif
