<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Khởi tạo tài khoản Admin ở bảng riêng biệt
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'full_name' => 'System Administrator',
        ]);
    }
}