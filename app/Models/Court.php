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

    /**
     * Get the tenant that owns the court
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the court type that this court belongs to
     */
    public function courtType()
    {
        return $this->belongsTo(CourtType::class);
    }

    /**
     * Get the reservations for this court
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the pricings for this court
     */
    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    /**
     * Get the current active pricing
     */
    public function getActivePricing()
    {
        return $this->pricings()
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get pricing for a specific date and time
     */
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

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to get active courts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get indoor courts
     */
    public function scopeIndoor($query)
    {
        return $query->where('is_indoor', true);
    }

    /**
     * Scope to get outdoor courts
     */
    public function scopeOutdoor($query)
    {
        return $query->where('is_indoor', false);
    }

    /**
     * Get facilities as array
     */
    public function getFacilitiesListAttribute()
    {
        if ($this->facilities) {
            return json_decode($this->facilities, true);
        }
        return [];
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get court type name
     */
    public function getCourtTypeNameAttribute()
    {
        return $this->courtType ? $this->courtType->name : 'N/A';
    }

    /**
     * Get tenant name
     */
    public function getTenantNameAttribute()
    {
        return $this->tenant ? $this->tenant->name : 'N/A';
    }
}