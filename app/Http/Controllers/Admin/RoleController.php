<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
        ]);

        Role::create($request->all());

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->all());

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role with associated users.');
        }
        
        // Prevent deleting default roles
        if (in_array($role->id, [1, 2, 3])) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete default system roles.');
        }
        
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}