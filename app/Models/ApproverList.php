<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApproverList extends Model
{
    protected $table = 'approver_lists';
    
    protected $fillable = [
        'DepartmentCode', 'level', 'employeeID',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employeeID');
    }
}