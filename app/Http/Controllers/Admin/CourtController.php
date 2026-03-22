<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Tenant;
use App\Models\CourtType;
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
                abort(403, 'Only administrators can manage courts.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $courts = Court::forTenant()->with('tenant', 'courtType')->latest()->paginate(10);
        return view('admin.courts.index', compact('courts'));
    }

    public function create()
    {
         $tenants = Tenant::where('is_active', true)->get();
    $courtTypes = CourtType::where('is_active', true)->get();
    $courtClassification = ['standard' => 'Standard', 'vip' => 'VIP', 'premium' => 'Premium'];
    $surfaces = ['clay' => 'Clay', 'grass' => 'Grass', 'hard' => 'Hard', 'carpet' => 'Carpet'];
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
    
    return view('admin.courts.create', compact('tenants', 'courtTypes', 'courtClassification', 'surfaces', 'facilities'));
    }

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['facilities', 'image']);
        
        if ($request->has('facilities')) {
            $data['facilities'] = json_encode($request->facilities);
        }
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courts', 'public');
            $data['image'] = $imagePath;
        }

        Court::create($data);

        return redirect()->route('admin.courts.index')
            ->with('success', 'Court created successfully.');
    }

    public function edit(Court $court)
    {
        $tenants = Tenant::where('is_active', true)->get();
        $courtTypes = CourtType::forTenant()->where('is_active', true)->get();
        $courtClassification = ['standard' => 'Standard', 'vip' => 'VIP', 'premium' => 'Premium'];
        $surfaces = ['clay' => 'Clay', 'grass' => 'Grass', 'hard' => 'Hard', 'carpet' => 'Carpet'];
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
        $selectedFacilities = $court->facilities ? json_decode($court->facilities, true) : [];
        
        return view('admin.courts.edit', compact('court', 'tenants', 'courtTypes', 'courtClassification', 'surfaces', 'facilities', 'selectedFacilities'));
    }

    public function update(Request $request, Court $court)
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['facilities', 'image']);
        
        if ($request->has('facilities')) {
            $data['facilities'] = json_encode($request->facilities);
        } else {
            $data['facilities'] = null;
        }
        
        if ($request->hasFile('image')) {
            if ($court->image) {
                Storage::disk('public')->delete($court->image);
            }
            $imagePath = $request->file('image')->store('courts', 'public');
            $data['image'] = $imagePath;
        }

        $court->update($data);

        return redirect()->route('admin.courts.index')
            ->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        if ($court->image) {
            Storage::disk('public')->delete($court->image);
        }
        
        $court->delete();
        
        return redirect()->route('admin.courts.index')
            ->with('success', 'Court deleted successfully.');
    }

    public function toggleStatus(Court $court)
    {
        $court->update(['is_active' => !$court->is_active]);
        $status = $court->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.courts.index')
            ->with('success', "Court {$status} successfully.");
    }
}