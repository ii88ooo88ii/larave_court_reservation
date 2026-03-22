<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'court_id',
        'user_id',
        'reservation_code',
        'reservation_date',
        'start_time',
        'end_time',
        'duration_hours',
        'base_price',
        'additional_fees_total',
        'total_amount',
        'status',
        'notes',
        'additional_fees_breakdown',
        'cancelled_at',
        'cancellation_reason',
        'is_extended',
        'extension_count',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'additional_fees_breakdown' => 'array',
        'is_extended' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    protected $appends = ['formatted_start_time', 'formatted_end_time'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedStartTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A');
    }

    public function getFormattedEndTimeAttribute()
    {
        return Carbon::parse($this->end_time)->format('h:i A');
    }

    // Calculate total price based on court pricing
    public function calculatePrice()
    {
        $court = $this->court;
        $date = Carbon::parse($this->reservation_date);
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);
        $duration = $this->duration_hours;
        
        // Get active pricing for this court
        $pricing = Pricing::where('court_id', $court->id)
            ->where('is_active', true)
            ->first();
        
        if (!$pricing) {
            return [
                'base_price' => 0,
                'additional_fees_total' => 0,
                'total_amount' => 0,
                'additional_fees_breakdown' => []
            ];
        }
        
        // Determine if it's peak time (weekend or after 6 PM)
        $dayOfWeek = $date->dayOfWeekIso; // 1=Monday, 7=Sunday
        $hour = (int)$startTime->format('H');
        
        $isPeak = false;
        $isWeekend = in_array($dayOfWeek, [6, 7]); // Saturday or Sunday
        $isEvening = $hour >= 18;
        
        // Custom logic for peak pricing - you can modify this
        if ($isWeekend || $isEvening) {
            $isPeak = true;
        }
        
        // Calculate base price
        if ($isPeak && $pricing->peak_price) {
            $pricePerHour = $pricing->peak_price;
        } elseif (!$isPeak && $pricing->off_peak_price) {
            $pricePerHour = $pricing->off_peak_price;
        } else {
            $pricePerHour = $pricing->base_price;
        }
        
        $basePrice = $pricePerHour * $duration;
        
        // Calculate additional fees
        $additionalFeesTotal = 0;
        $additionalFeesBreakdown = [];
        
        $additionalFees = AdditionalFee::where('pricing_id', $pricing->id)
            ->where('is_active', true)
            ->get();
        
        foreach ($additionalFees as $fee) {
            if ($fee->type === 'percentage') {
                $feeAmount = $basePrice * ($fee->amount / 100);
            } else {
                $feeAmount = $fee->amount;
            }
            
            $additionalFeesTotal += $feeAmount;
            $additionalFeesBreakdown[] = [
                'name' => $fee->name,
                'type' => $fee->type,
                'amount' => $fee->amount,
                'calculated_amount' => $feeAmount,
                'is_mandatory' => $fee->is_mandatory,
            ];
        }
        
        $totalAmount = $basePrice + $additionalFeesTotal;
        
        return [
            'base_price' => $basePrice,
            'additional_fees_total' => $additionalFeesTotal,
            'total_amount' => $totalAmount,
            'additional_fees_breakdown' => $additionalFeesBreakdown,
            'price_per_hour' => $pricePerHour,
            'is_peak' => $isPeak,
            'pricing_name' => $pricing->name,
        ];
    }
}