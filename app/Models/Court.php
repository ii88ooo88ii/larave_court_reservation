<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'court_type_id',
        'name',
        'type',
        'surface',
        'is_indoor',
        'has_floodlights',
        'capacity',
        'description',
        'facilities',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_indoor' => 'boolean',
        'has_floodlights' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function courtType()
    {
        return $this->belongsTo(CourtType::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function getCurrentPricing()
    {
        return $this->pricings()
            ->where('is_active', true)
            ->where('type', 'standard')
            ->first();
    }

    public function getPricingForDateTime($dateTime)
    {
        $dayOfWeek = $dateTime->dayOfWeekIso; // 1-7 (Monday=1, Sunday=7)
        $time = $dateTime->format('H:i:s');
        
        $pricing = $this->pricings()
            ->where('is_active', true)
            ->where(function($q) use ($dayOfWeek, $time) {
                $q->whereJsonContains('days_of_week', $dayOfWeek)
                  ->where('start_time', '<=', $time)
                  ->where('end_time', '>=', $time);
            })
            ->orWhere(function($q) {
                $q->whereNull('days_of_week')
                  ->where('type', 'standard');
            })
            ->first();
            
        return $pricing;
    }

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }
}