<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Bỏ qua dòng trống
        if (empty($row['username']) || empty($row['full_name'])) {
            return null;
        }

        // Kiểm tra tránh trùng lặp Username hoặc Email (nếu email không trống)
        $exists = User::where('username', $row['username'])
            ->when(!empty($row['email']), function ($q) use ($row) {
                return $q->orWhere('email', $row['email']);
            })
            ->first();

        if ($exists) {
            return null; // Bỏ qua bản ghi đã tồn tại hoặc email đã có
        }

        return new User([
            'username' => $row['username'],
            'password' => Hash::make($row['password'] ?? '123456'),
            'full_name' => $row['full_name'],
            'email' => !empty($row['email']) ? $row['email'] : null,
            'DepartmentCode' => $row['departmentcode'] ?? null,
            'department_name' => $row['department_name'] ?? null,
            'job_title' => $row['job_title'] ?? null,
            'role_id' => 2, // Mặc định là nhân viên
            'status' => 1,
        ]);
    }
}