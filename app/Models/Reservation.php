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
}