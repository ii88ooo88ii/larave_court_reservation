<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    // List all tenants
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $tenants = Tenant::withCount('users')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    // Get single tenant
    public function show($id)
    {
        $tenant = Tenant::withCount('users')->find($id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $tenant
        ]);
    }

    // Create new tenant
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        
        $tenant = Tenant::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'data' => $tenant
        ], 201);
    }

    // Update tenant
    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tenant updated successfully',
            'data' => $tenant
        ]);
    }

    // Delete tenant
    public function destroy($id)
    {
        $tenant = Tenant::find($id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        
        // Check if tenant has users
        if ($tenant->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete tenant with associated users'
            ], 422);
        }
        
        $tenant->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully'
        ]);
    }

    // Toggle tenant status
    public function toggleStatus($id)
    {
        $tenant = Tenant::find($id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }
        
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Tenant {$status} successfully",
            'data' => $tenant
        ]);
    }
}