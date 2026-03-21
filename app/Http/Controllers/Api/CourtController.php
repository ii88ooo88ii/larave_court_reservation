<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function __construct()
    {
        // Only allow admin access
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can manage courts.'
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of courts
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $tenantId = $request->get('tenant_id');
        $isActive = $request->get('is_active');
        $type = $request->get('type');
        
        $query = Court::with('tenant');
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $courts = $query->latest()->paginate($perPage);
        
        // Transform data to include full image URL
        $courts->getCollection()->transform(function ($court) {
            if ($court->image) {
                $court->image_url = Storage::url($court->image);
            }
            if ($court->facilities) {
                $court->facilities_list = json_decode($court->facilities, true);
            }
            return $court;
        });
        
        return response()->json([
            'success' => true,
            'data' => $courts,
            'message' => 'Courts retrieved successfully'
        ]);
    }

    /**
     * Store a newly created court
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:standard,vip,premium',
            'surface' => 'nullable|string|max:50',
            'is_indoor' => 'boolean',
            'has_floodlights' => 'boolean',
            'hourly_rate' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'facilities' => 'array',
            'facilities.*' => 'string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['facilities', 'image']);
        
        // Handle facilities (store as JSON)
        if ($request->has('facilities')) {
            $data['facilities'] = json_encode($request->facilities);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courts', 'public');
            $data['image'] = $imagePath;
        }

        $court = Court::create($data);
        
        // Load tenant relationship
        $court->load('tenant');
        
        // Add image URL to response
        if ($court->image) {
            $court->image_url = Storage::url($court->image);
        }
        if ($court->facilities) {
            $court->facilities_list = json_decode($court->facilities, true);
        }

        return response()->json([
            'success' => true,
            'data' => $court,
            'message' => 'Court created successfully'
        ], 201);
    }

    /**
     * Display the specified court
     */
    public function show($id)
    {
        $court = Court::with('tenant')->find($id);
        
        if (!$court) {
            return response()->json([
                'success' => false,
                'message' => 'Court not found'
            ], 404);
        }
        
        // Add image URL
        if ($court->image) {
            $court->image_url = Storage::url($court->image);
        }
        if ($court->facilities) {
            $court->facilities_list = json_decode($court->facilities, true);
        }
        
        return response()->json([
            'success' => true,
            'data' => $court,
            'message' => 'Court retrieved successfully'
        ]);
    }

    /**
     * Update the specified court
     */
    public function update(Request $request, $id)
    {
        $court = Court::find($id);
        
        if (!$court) {
            return response()->json([
                'success' => false,
                'message' => 'Court not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'sometimes|exists:tenants,id',
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:standard,vip,premium',
            'surface' => 'nullable|string|max:50',
            'is_indoor' => 'boolean',
            'has_floodlights' => 'boolean',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'capacity' => 'sometimes|integer|min:1|max:10',
            'description' => 'nullable|string',
            'facilities' => 'array',
            'facilities.*' => 'string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['facilities', 'image']);
        
        // Handle facilities
        if ($request->has('facilities')) {
            $data['facilities'] = json_encode($request->facilities);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($court->image) {
                Storage::disk('public')->delete($court->image);
            }
            $imagePath = $request->file('image')->store('courts', 'public');
            $data['image'] = $imagePath;
        }

        $court->update($data);
        
        // Load tenant relationship
        $court->load('tenant');
        
        // Add image URL to response
        if ($court->image) {
            $court->image_url = Storage::url($court->image);
        }
        if ($court->facilities) {
            $court->facilities_list = json_decode($court->facilities, true);
        }

        return response()->json([
            'success' => true,
            'data' => $court,
            'message' => 'Court updated successfully'
        ]);
    }

    /**
     * Remove the specified court
     */
    public function destroy($id)
    {
        $court = Court::find($id);
        
        if (!$court) {
            return response()->json([
                'success' => false,
                'message' => 'Court not found'
            ], 404);
        }
        
        // Delete image if exists
        if ($court->image) {
            Storage::disk('public')->delete($court->image);
        }
        
        $court->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Court deleted successfully'
        ]);
    }

    /**
     * Toggle court status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        $court = Court::find($id);
        
        if (!$court) {
            return response()->json([
                'success' => false,
                'message' => 'Court not found'
            ], 404);
        }
        
        $court->update(['is_active' => !$court->is_active]);
        $status = $court->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'data' => $court,
            'message' => "Court {$status} successfully"
        ]);
    }
    
    /**
     * Get court types
     */
    public function getTypes()
    {
        $types = [
            'standard' => 'Standard Court',
            'vip' => 'VIP Court',
            'premium' => 'Premium Court'
        ];
        
        return response()->json([
            'success' => true,
            'data' => $types,
            'message' => 'Court types retrieved successfully'
        ]);
    }
    
    /**
     * Get surface types
     */
    public function getSurfaces()
    {
        $surfaces = [
            'clay' => 'Clay',
            'grass' => 'Grass',
            'hard' => 'Hard',
            'carpet' => 'Carpet'
        ];
        
        return response()->json([
            'success' => true,
            'data' => $surfaces,
            'message' => 'Surface types retrieved successfully'
        ]);
    }
    
    /**
     * Get available facilities
     */
    public function getFacilities()
    {
        $facilities = [
            'locker_room' => 'Locker Room',
            'shower' => 'Shower',
            'parking' => 'Parking',
            'cafe' => 'Cafe',
            'pro_shop' => 'Pro Shop',
            'lighting' => 'Lighting',
            'seating' => 'Seating Area',
            'water' => 'Water Station'
        ];
        
        return response()->json([
            'success' => true,
            'data' => $facilities,
            'message' => 'Facilities retrieved successfully'
        ]);
    }
}