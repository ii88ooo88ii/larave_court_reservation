<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Court;
use App\Models\User;
use App\Models\Pricing;
use App\Models\AdditionalFee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can manage reservations.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $query = Reservation::with(['court', 'user', 'tenant'])
            ->whereDate('reservation_date', $date);
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        $reservations = $query->orderBy('start_time')->paginate(20);
        
        return view('admin.reservations.index', compact('reservations', 'date', 'status'));
    }

    public function create()
    {
        $courts = Court::where('is_active', true)->get();
        $users = User::all();
        
        return view('admin.reservations.create', compact('courts', 'users'));
    }

    public function calculatePrice(Request $request)
    {
        $courtId = $request->court_id;
        $date = $request->reservation_date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        // Calculate duration
        $duration = Carbon::parse($startTime)->diffInHours(Carbon::parse($endTime));
        
        // Get court pricing
        $pricing = Pricing::where('court_id', $courtId)
            ->where('is_active', true)
            ->first();
        
        if (!$pricing) {
            return response()->json([
                'success' => false,
                'message' => 'No pricing configured for this court.'
            ]);
        }
        
        // Determine peak or off-peak based on day and time
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->dayOfWeekIso;
        $hour = (int)Carbon::parse($startTime)->format('H');
        
        $isWeekend = in_array($dayOfWeek, [6, 7]);
        $isEvening = $hour >= 18;
        $isPeak = $isWeekend || $isEvening;
        
        // Get price per hour
        if ($isPeak && $pricing->peak_price) {
            $pricePerHour = (float)$pricing->peak_price;
            $priceType = 'Peak Rate (Weekend/Evening)';
        } elseif (!$isPeak && $pricing->off_peak_price) {
            $pricePerHour = (float)$pricing->off_peak_price;
            $priceType = 'Off-Peak Rate';
        } else {
            $pricePerHour = (float)$pricing->base_price;
            $priceType = 'Standard Rate';
        }
        
        $basePrice = $pricePerHour * $duration;
        
        // Calculate additional fees
        $additionalFees = AdditionalFee::where('pricing_id', $pricing->id)
            ->where('is_active', true)
            ->get();
        
        $additionalFeesTotal = 0;
        $feesBreakdown = [];
        
        foreach ($additionalFees as $fee) {
            if ($fee->type === 'percentage') {
                $feeAmount = $basePrice * ((float)$fee->amount / 100);
            } else {
                $feeAmount = (float)$fee->amount;
            }
            
            $additionalFeesTotal += $feeAmount;
            $feesBreakdown[] = [
                'name' => $fee->name,
                'type' => $fee->type,
                'amount' => (float)$fee->amount,
                'calculated_amount' => $feeAmount,
            ];
        }
        
        $totalAmount = $basePrice + $additionalFeesTotal;
        
        return response()->json([
            'success' => true,
            'duration' => (int)$duration,
            'price_per_hour' => $pricePerHour,
            'price_type' => $priceType,
            'base_price' => $basePrice,
            'additional_fees_total' => $additionalFeesTotal,
            'additional_fees_breakdown' => $feesBreakdown,
            'total_amount' => $totalAmount,
            'currency' => $pricing->currency ?? 'USD'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'court_id' => 'required|exists:courts,id',
            'user_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check availability
        $isAvailable = !Reservation::where('court_id', $request->court_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if (!$isAvailable) {
            return redirect()->back()->with('error', 'This time slot is already booked.')->withInput();
        }

        $duration = Carbon::parse($request->start_time)->diffInHours(Carbon::parse($request->end_time));
        $reservationCode = 'RES-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
        
        // Handle additional_fees_breakdown - ensure it's stored as JSON string
        $additionalFeesBreakdown = $request->additional_fees_breakdown;
        if (is_array($additionalFeesBreakdown)) {
            $additionalFeesBreakdown = json_encode($additionalFeesBreakdown);
        } elseif (is_string($additionalFeesBreakdown)) {
            // Already a string, keep as is
        } else {
            $additionalFeesBreakdown = null;
        }
        
        $reservation = Reservation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'court_id' => $request->court_id,
            'user_id' => $request->user_id,
            'reservation_code' => $reservationCode,
            'reservation_date' => $request->reservation_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_hours' => $duration,
            'base_price' => $request->base_price,
            'additional_fees_total' => $request->additional_fees_total,
            'total_amount' => $request->total_amount,
            'status' => 'confirmed',
            'notes' => $request->notes,
            'additional_fees_breakdown' => $additionalFeesBreakdown,
        ]);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $courts = Court::where('is_active', true)->get();
        $users = User::all();
        
        return view('admin.reservations.edit', compact('reservation', 'courts', 'users'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        if ($reservation->status === 'completed' || $reservation->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot modify completed or cancelled reservations.');
        }
        
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $reservation->update([
            'notes' => $request->notes,
            'status' => $request->status ?? $reservation->status,
        ]);

        if ($request->status === 'cancelled') {
            $reservation->update([
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);
        }

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function calendar(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $courts = Court::where('is_active', true)->get();
        
        $reservations = Reservation::with(['court', 'user'])
            ->whereDate('reservation_date', $date)
            ->get()
            ->groupBy('court_id');
        
        return view('admin.reservations.calendar', compact('courts', 'reservations', 'date'));
    }
}