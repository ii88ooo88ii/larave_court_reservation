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
        $tenants = Tenant::withCount('users')->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
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
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
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
        if ($tenant->users()->count() > 0) {
            return redirect()->route('admin.tenants.index')->with('error', 'Cannot delete tenant with associated users.');
        }
        
        $tenant->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.tenants.index')->with('success', "Tenant {$status} successfully.");
    }
}