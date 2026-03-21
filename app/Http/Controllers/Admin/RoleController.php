<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        if (!auth()->user()->canViewRoles()) {
            abort(403, 'You don\'t have permission to view roles.');
        }
        
        $roles = Role::withCount('users')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->user()->canCreateRoles()) {
            abort(403, 'You don\'t have permission to create roles.');
        }
        
        $permissions = Permission::getGroupedPermissions();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canCreateRoles()) {
            abort(403, 'You don\'t have permission to create roles.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
        ]);

        // Sync permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully with permissions.');
    }

    public function edit(Role $role)
    {
        if (!auth()->user()->canEditRoles()) {
            abort(403, 'You don\'t have permission to edit roles.');
        }
        
        $permissions = Permission::getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->canEditRoles()) {
            abort(403, 'You don\'t have permission to edit roles.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
        ]);

        // Sync permissions
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully with permissions.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->canDeleteRoles()) {
            abort(403, 'You don\'t have permission to delete roles.');
        }
        
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role with associated users.');
        }
        
        if (in_array($role->id, [1, 2, 3])) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete default system roles.');
        }
        
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    // Show permissions for role (separate page)
    public function permissions(Role $role)
    {
        if (!auth()->user()->hasPermission('roles.manage-permissions')) {
            abort(403, 'You don\'t have permission to manage role permissions.');
        }
        
        $permissions = Permission::getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    // Update role permissions (separate page)
    public function updatePermissions(Request $request, Role $role)
    {
        if (!auth()->user()->hasPermission('roles.manage-permissions')) {
            abort(403, 'You don\'t have permission to manage role permissions.');
        }
        
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Permissions updated successfully for role: ' . $role->name);
    }
}