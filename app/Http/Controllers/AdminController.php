<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ApproverList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersTemplateExport;
use App\Imports\UsersImport;


class AdminController extends Controller
{
    // --- QUẢN LÝ TÀI KHOẢN ADMIN ---
    public function password()
    {
        return view('admin.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        // Auth::guard('admin')->user() trả về model Admin. Chúng ta có thể dùng Model queries để update
        // Hoặc update model instance directly
        \App\Models\Admin::where('id', $admin->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    // --- QUẢN LÝ USER ---
    public function indexUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('department')) {
            $query->where('DepartmentCode', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Lấy danh sách nhân viên từ Database, sắp xếp mới nhất lên đầu, có phân trang
        $users = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        
        $departments = User::whereNotNull('DepartmentCode')
            ->where('DepartmentCode', '!=', '')
            ->select('DepartmentCode', 'department_name')
            ->distinct()
            ->get();

        if ($request->ajax()) {
            return view('admin.users.table', compact('users'))->render();
        }

        // Trả về view kèm theo biến $users và $departments
        return view('admin.users.index', compact('users', 'departments'));
    }

    // [THÊM MỚI] Giao diện tạo nhân viên
    public function createUser()
    {
        return view('admin.users.create');
    }

    // [THÊM MỚI] Xử lý lưu nhân viên mới
   public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'status' => 'required|boolean',
            'DepartmentCode' => 'nullable|string|max:50',
            'department_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['username', 'full_name', 'email', 'status', 'DepartmentCode', 'department_name', 'job_title']);
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = 2; // Tự động gán là nhân viên mặc định

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Thêm nhân viên mới thành công!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'status' => 'required|boolean',
            'DepartmentCode' => 'nullable|string|max:50',
            'department_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:4',
        ]);

        $data = $request->only(['full_name', 'email', 'status', 'DepartmentCode', 'department_name', 'job_title']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    // --- QUẢN LÝ DANH SÁCH PHÊ DUYỆT ---
    public function indexApprovers()
    {
        $approvers = ApproverList::with('employee')->orderBy('id', 'desc')->get();
        $users = User::where('status', 1)->get();
        
        // Lấy danh sách các mã phòng ban đang tồn tại ở bảng Users (loại bỏ null)
        $departments = User::whereNotNull('DepartmentCode')->where('DepartmentCode', '!=', '')->distinct()->pluck('DepartmentCode');

        return view('admin.approvers.index', compact('approvers', 'users', 'departments'));
    }

    public function storeApprover(Request $request)
    {
        $request->validate([
            'DepartmentCode' => 'required|string|max:50',
            'level' => 'required|integer',
            'employeeID' => 'required|exists:users,id',
        ]);

        ApproverList::create($request->all());

        return redirect()->route('admin.approvers.index')->with('success', 'Thêm cấu hình phê duyệt thành công!');
    }

    public function editApprover($id)
    {
        $approver = ApproverList::findOrFail($id);
        $users = User::where('status', 1)->get();
        $departments = User::whereNotNull('DepartmentCode')->where('DepartmentCode', '!=', '')->distinct()->pluck('DepartmentCode');

        return view('admin.approvers.edit', compact('approver', 'users', 'departments'));
    }

    public function updateApprover(Request $request, $id)
    {
        $approver = ApproverList::findOrFail($id);

        $request->validate([
            'DepartmentCode' => 'required|string|max:50',
            'level' => 'required|integer',
            'employeeID' => 'required|exists:users,id',
        ]);

        $approver->update($request->all());

        return redirect()->route('admin.approvers.index')->with('success', 'Cập nhật cấu hình phê duyệt thành công!');
    }

    public function destroyApprover($id)
    {
        $approver = ApproverList::findOrFail($id);
        $approver->delete();

        return redirect()->route('admin.approvers.index')->with('success', 'Xóa cấu hình phê duyệt thành công!');
    }
    public function downloadTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'Template_Import_Users.xlsx');
    }
    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // Tối đa 5MB
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('admin.users.index')->with('success', 'Nhập danh sách nhân viên từ Excel thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->withErrors(['file' => 'Lỗi định dạng file hoặc dữ liệu: ' . $e->getMessage()]);
        }
    }

    // --- QUẢN LÝ PHÂN QUYỀN HỆ THỐNG ---
    public function indexPermissions()
    {
        // Nhóm các quyền theo người dùng
        $userPermissions = \App\Models\UserPermission::with('user')->get()->groupBy('user_id');
        $users = User::where('status', 1)->get();
        
        return view('admin.permissions.index', compact('userPermissions', 'users'));
    }

    public function storePermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'required|array',
        ]);

        $userId = $request->user_id;

        // Xóa tất cả các quyền hiện tại của user này để cấp lại
        \App\Models\UserPermission::where('user_id', $userId)->delete();

        // Thêm các quyền mới
        foreach ($request->permissions as $perm) {
            \App\Models\UserPermission::create([
                'user_id' => $userId,
                'permission' => $perm
            ]);
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Đã phân quyền thành công!');
    }

    public function destroyPermissions($id)
    {
        // $id ở đây là user_id, xóa tất cả quyền của người này
        \App\Models\UserPermission::where('user_id', $id)->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Đã gỡ tất cả quyền thành công!');
    }
}