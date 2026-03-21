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
        'hourly_rate',
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
        'hourly_rate' => 'decimal:2',
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

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }

    public function getActivePricing()
    {
        return $this->pricings()->where('is_active', true)->first();
    }

    public function getPricingForTime($dayOfWeek, $time = null)
    {
        $query = $this->pricings()
            ->where('is_active', true)
            ->where('tenant_id', $this->tenant_id);
        
        if ($time) {
            $query->where(function($q) use ($time) {
                $q->where('start_time', '<=', $time)
                  ->where('end_time', '>=', $time);
            });
        }
        
        if ($dayOfWeek) {
            $query->whereJsonContains('days_of_week', $dayOfWeek);
        }
        
        return $query->first();
    }
}