<?php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentLeaves = LeaveRequest::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
                                    
        return view('dashboard', compact('recentLeaves'));
    }
}