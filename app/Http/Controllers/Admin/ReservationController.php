<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Court;
use App\Models\User;
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
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $reservations = Reservation::with(['court', 'user'])
            ->whereDate('reservation_date', $date)
            ->orderBy('start_time')
            ->paginate(20);
        
        return view('admin.reservations.index', compact('reservations', 'date'));
    }

    public function create()
    {
        // Remove forTenant() scope - just get all active courts and users
        $courts = Court::where('is_active', true)->get();
        $users = User::all(); // or User::where('is_active', true)->get() if you have that field
        
        return view('admin.reservations.create', compact('courts', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'court_id' => 'required|exists:courts,id',
            'user_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
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
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if (!$isAvailable) {
            return redirect()->back()->with('error', 'This time slot is already booked.')->withInput();
        }

        $duration = Carbon::parse($request->start_time)->diffInHours(Carbon::parse($request->end_time));
        $reservationCode = 'RES-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
        
        $reservation = Reservation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'court_id' => $request->court_id,
            'user_id' => $request->user_id,
            'reservation_code' => $reservationCode,
            'reservation_date' => $request->reservation_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_hours' => $duration,
            'base_price' => 0,
            'additional_fees_total' => 0,
            'total_amount' => 0,
            'status' => 'confirmed',
            'notes' => $request->notes,
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

    public function cancel(Reservation $reservation, Request $request)
    {
        if ($reservation->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel completed reservations.');
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }

    public function checkAvailability(Request $request)
    {
        // Simplified version
        return response()->json(['available' => true]);
    }

    public function getAvailableSlots(Request $request)
    {
        // Simplified version
        return response()->json(['slots' => []]);
    }

    public function extend(Request $request, Reservation $reservation)
    {
        return response()->json(['success' => false, 'message' => 'Extension not implemented yet']);
    }
}