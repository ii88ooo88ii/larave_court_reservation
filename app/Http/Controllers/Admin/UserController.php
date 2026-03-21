<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->canViewUsers()) {
            abort(403, 'You don\'t have permission to view users.');
        }
        
        $users = User::with(['role', 'tenant'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->canCreateUsers()) {
            abort(403, 'You don\'t have permission to create users.');
        }
        
        $roles = Role::all();
        $tenants = Tenant::all();
        return view('admin.users.create', compact('roles', 'tenants'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canCreateUsers()) {
            abort(403, 'You don\'t have permission to create users.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'tenant_id' => $request->tenant_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->canEditUsers()) {
            abort(403, 'You don\'t have permission to edit users.');
        }
        
        $roles = Role::all();
        $tenants = Tenant::all();
        return view('admin.users.edit', compact('user', 'roles', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->canEditUsers()) {
            abort(403, 'You don\'t have permission to edit users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'tenant_id' => $request->tenant_id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        if (!auth()->user()->canDeleteUsers()) {
            abort(403, 'You don\'t have permission to delete users.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}