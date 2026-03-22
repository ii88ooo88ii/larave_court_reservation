<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalFee;
use App\Models\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdditionalFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can manage additional fees.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $pricingId = $request->get('pricing_id');
        $query = AdditionalFee::with('pricing');
        
        if ($pricingId) {
            $query->where('pricing_id', $pricingId);
        }
        
        $fees = $query->latest()->paginate(10);
        $pricings = Pricing::where('is_active', true)->get();
        
        return view('admin.additional-fees.index', compact('fees', 'pricings', 'pricingId'));
    }

    public function create(Request $request)
    {
        $pricings = Pricing::where('is_active', true)->get();
        $selectedPricing = $request->get('pricing_id');
        $feeTypes = [
            'fixed' => 'Fixed Amount',
            'percentage' => 'Percentage of Subtotal'
        ];
        
        return view('admin.additional-fees.create', compact('pricings', 'selectedPricing', 'feeTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pricing_id' => 'required|exists:pricings,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
            'is_mandatory' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['tenant_id'] = auth()->user()->tenant_id;
        
        AdditionalFee::create($data);

        return redirect()->route('admin.additional-fees.index', ['pricing_id' => $request->pricing_id])
            ->with('success', 'Additional fee created successfully.');
    }

    public function edit(AdditionalFee $additionalFee)
    {
        $pricings = Pricing::where('is_active', true)->get();
        $feeTypes = [
            'fixed' => 'Fixed Amount',
            'percentage' => 'Percentage of Subtotal'
        ];
        
        return view('admin.additional-fees.edit', compact('additionalFee', 'pricings', 'feeTypes'));
    }

    public function update(Request $request, AdditionalFee $additionalFee)
    {
        $validator = Validator::make($request->all(), [
            'pricing_id' => 'required|exists:pricings,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
            'is_mandatory' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $additionalFee->update($request->all());

        return redirect()->route('admin.additional-fees.index', ['pricing_id' => $additionalFee->pricing_id])
            ->with('success', 'Additional fee updated successfully.');
    }

    public function destroy(AdditionalFee $additionalFee)
    {
        $pricingId = $additionalFee->pricing_id;
        $additionalFee->delete();
        
        return redirect()->route('admin.additional-fees.index', ['pricing_id' => $pricingId])
            ->with('success', 'Additional fee deleted successfully.');
    }

    public function toggleStatus(AdditionalFee $additionalFee)
    {
        $additionalFee->update(['is_active' => !$additionalFee->is_active]);
        $status = $additionalFee->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.additional-fees.index', ['pricing_id' => $additionalFee->pricing_id])
            ->with('success', "Additional fee {$status} successfully.");
    }
}