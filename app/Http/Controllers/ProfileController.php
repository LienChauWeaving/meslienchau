<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function password()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function delegate()
    {
        $user = Auth::user();
        // Lấy danh sách các nhân viên khác (để uỷ quyền)
        $users = User::where('id', '!=', $user->id)
                     ->where('status', 1)
                     ->get();

        return view('profile.delegate', compact('user', 'users'));
    }

    public function updateDelegate(Request $request)
    {
        $request->validate([
            'delegate_id' => 'nullable|exists:users,id'
        ]);

        $user = Auth::user();
        $user->update([
            'delegate_id' => $request->delegate_id
        ]);

        if ($request->delegate_id) {
            return redirect()->back()->with('success', 'Đã lưu cấu hình uỷ quyền thành công!');
        } else {
            return redirect()->back()->with('success', 'Đã gỡ cấu hình uỷ quyền thành công!');
        }
    }
}
