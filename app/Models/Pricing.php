<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'court_id',
        'name',
        'type',
        'price',
        'currency',
        'start_time',
        'end_time',
        'days_of_week',
        'minimum_hours',
        'maximum_hours',
        'is_active',
        'description',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }

    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }
    
    public function getDaysOfWeekLabelsAttribute()
    {
        if (!$this->days_of_week) return null;
        
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
        
        $selectedDays = [];
        foreach ($this->days_of_week as $day) {
            $selectedDays[] = $days[$day];
        }
        
        return implode(', ', $selectedDays);
    }
}