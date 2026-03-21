<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pricing;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can manage pricing.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $courtId = $request->get('court_id');
        $query = Pricing::forTenant()->with('court');
        
        if ($courtId) {
            $query->where('court_id', $courtId);
        }
        
        $pricings = $query->latest()->paginate(10);
        $courts = Court::forTenant()->where('is_active', true)->get();
        
        return view('admin.pricings.index', compact('pricings', 'courts', 'courtId'));
    }

    public function create(Request $request)
    {
        $courts = Court::forTenant()->where('is_active', true)->get();
        $selectedCourt = $request->get('court_id');
        $pricingTypes = [
            'hourly' => 'Hourly Rate',
            'daily' => 'Daily Rate',
            'weekly' => 'Weekly Rate',
            'monthly' => 'Monthly Rate',
            'package' => 'Package Deal'
        ];
        
        return view('admin.pricings.create', compact('courts', 'selectedCourt', 'pricingTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'court_id' => 'required|exists:courts,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:hourly,daily,weekly,monthly,package',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'minimum_hours' => 'required|integer|min:1',
            'maximum_hours' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['tenant_id'] = auth()->user()->tenant_id;
        
        if ($request->has('days_of_week')) {
            $data['days_of_week'] = json_encode($request->days_of_week);
        }
        
        Pricing::create($data);

        return redirect()->route('admin.pricings.index', ['court_id' => $request->court_id])
            ->with('success', 'Pricing created successfully.');
    }

    public function edit(Pricing $pricing)
    {
        $courts = Court::forTenant()->where('is_active', true)->get();
        $pricingTypes = [
            'hourly' => 'Hourly Rate',
            'daily' => 'Daily Rate',
            'weekly' => 'Weekly Rate',
            'monthly' => 'Monthly Rate',
            'package' => 'Package Deal'
        ];
        
        return view('admin.pricings.edit', compact('pricing', 'courts', 'pricingTypes'));
    }

    public function update(Request $request, Pricing $pricing)
    {
        $validator = Validator::make($request->all(), [
            'court_id' => 'required|exists:courts,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:hourly,daily,weekly,monthly,package',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'minimum_hours' => 'required|integer|min:1',
            'maximum_hours' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        
        if ($request->has('days_of_week')) {
            $data['days_of_week'] = json_encode($request->days_of_week);
        }
        
        $pricing->update($data);

        return redirect()->route('admin.pricings.index', ['court_id' => $pricing->court_id])
            ->with('success', 'Pricing updated successfully.');
    }

    public function destroy(Pricing $pricing)
    {
        $courtId = $pricing->court_id;
        $pricing->delete();
        
        return redirect()->route('admin.pricings.index', ['court_id' => $courtId])
            ->with('success', 'Pricing deleted successfully.');
    }

    public function toggleStatus(Pricing $pricing)
    {
        $pricing->update(['is_active' => !$pricing->is_active]);
        $status = $pricing->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.pricings.index', ['court_id' => $pricing->court_id])
            ->with('success', "Pricing {$status} successfully.");
    }
}