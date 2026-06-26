<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'username',
            'password',
            'full_name',
            'email',
            'departmentcode',
            'department_name',
            'job_title'
        ];
    }

    public function array(): array
    {
        return [
            ['nv01', '123456', 'Nguyen Van A', 'nva@company.com', 'IT', 'Phòng Công nghệ', 'Nhân viên'],
            ['nv02', '123456', 'Tran Thi B', '', 'HR', 'Phòng Nhân sự', 'Trưởng phòng'],
        ];
    }
}