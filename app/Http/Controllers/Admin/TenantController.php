<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        if (!auth()->user()->canViewTenants()) {
            abort(403, 'You don\'t have permission to view tenants.');
        }
        
        $tenants = Tenant::withCount('users')->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        if (!auth()->user()->canCreateTenants()) {
            abort(403, 'You don\'t have permission to create tenants.');
        }
        
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canCreateTenants()) {
            abort(403, 'You don\'t have permission to create tenants.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        
        Tenant::create($data);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function edit(Tenant $tenant)
    {
        if (!auth()->user()->canEditTenants()) {
            abort(403, 'You don\'t have permission to edit tenants.');
        }
        
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        if (!auth()->user()->canEditTenants()) {
            abort(403, 'You don\'t have permission to edit tenants.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $tenant->update($request->all());

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        if (!auth()->user()->canDeleteTenants()) {
            abort(403, 'You don\'t have permission to delete tenants.');
        }
        
        if ($tenant->users()->count() > 0) {
            return redirect()->route('admin.tenants.index')->with('error', 'Cannot delete tenant with associated users.');
        }
        
        $tenant->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function toggleStatus(Tenant $tenant)
    {
        if (!auth()->user()->canEditTenants()) {
            abort(403, 'You don\'t have permission to edit tenants.');
        }
        
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.tenants.index')->with('success', "Tenant {$status} successfully.");
    }
}