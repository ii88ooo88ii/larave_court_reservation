<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourtType;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourtTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can manage court types.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $courtTypes = CourtType::forTenant()
            ->with('tenant', 'courts')
            ->latest()
            ->paginate(10);
        return view('admin.court-types.index', compact('courtTypes'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->get();
        $icons = CourtType::getAvailableIcons();
        return view('admin.court-types.create', compact('tenants', 'icons'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        
        CourtType::create($data);

        return redirect()->route('admin.court-types.index')
            ->with('success', 'Court type created successfully.');
    }

    public function edit(CourtType $courtType)
    {
        $tenants = Tenant::where('is_active', true)->get();
        $icons = CourtType::getAvailableIcons();
        return view('admin.court-types.edit', compact('courtType', 'tenants', 'icons'));
    }

    public function update(Request $request, CourtType $courtType)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $courtType->update($request->all());

        return redirect()->route('admin.court-types.index')
            ->with('success', 'Court type updated successfully.');
    }

    public function destroy(CourtType $courtType)
    {
        if ($courtType->courts()->count() > 0) {
            return redirect()->route('admin.court-types.index')
                ->with('error', 'Cannot delete court type with associated courts.');
        }
        
        $courtType->delete();
        
        return redirect()->route('admin.court-types.index')
            ->with('success', 'Court type deleted successfully.');
    }

    public function toggleStatus(CourtType $courtType)
    {
        $courtType->update(['is_active' => !$courtType->is_active]);
        $status = $courtType->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.court-types.index')
            ->with('success', "Court type {$status} successfully.");
    }
}