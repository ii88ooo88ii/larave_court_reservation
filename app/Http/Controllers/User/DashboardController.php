<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ensure session is maintained
        $request->session()->put('user_dashboard_accessed', true);
        
        return view('admin.users.dashboard-regular');
    }
}