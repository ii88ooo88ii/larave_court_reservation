<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect regular users to their dashboard
        if (auth()->user()->isRegularUser()) {
            return redirect()->route('user.dashboard');
        }
        
        // Check permission for dashboard
        if (!auth()->user()->hasPermission('dashboard.view')) {
            abort(403, 'You don\'t have permission to view the dashboard.');
        }
        
        // Get real-time statistics
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalTenants = Tenant::count();
        
        // Get recent users (last 5)
        $recentUsers = User::with(['role', 'tenant'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get active tenants count
        $activeTenants = Tenant::where('is_active', true)->count();
        
        // Get users by role for chart
        $usersByRole = Role::withCount('users')
            ->get()
            ->map(function($role) {
                return [
                    'name' => $role->name,
                    'count' => $role->users_count
                ];
            });
        
        // Get users by tenant for chart
        $usersByTenant = Tenant::withCount('users')
            ->where('is_active', true)
            ->get()
            ->map(function($tenant) {
                return [
                    'name' => $tenant->name,
                    'count' => $tenant->users_count
                ];
            });
        
        // Check user permissions for redirections
        $canViewUsers = auth()->user()->canViewUsers();
        $canViewRoles = auth()->user()->canViewRoles();
        $canViewTenants = auth()->user()->canViewTenants();
        $isAdmin = auth()->user()->isAdmin();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRoles', 
            'totalTenants',
            'activeTenants',
            'recentUsers',
            'usersByRole',
            'usersByTenant',
            'canViewUsers',
            'canViewRoles',
            'canViewTenants',
            'isAdmin'
        ));
    }
}