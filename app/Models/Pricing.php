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
        'base_price',
        'peak_price',
        'off_peak_price',
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
        'base_price' => 'decimal:2',
        'peak_price' => 'decimal:2',
        'off_peak_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function additionalFees()
    {
        return $this->hasMany(AdditionalFee::class);
    }

    public function calculatePrice($hours, $isPeak = false)
    {
        $price = $isPeak ? $this->peak_price : ($this->off_peak_price ?: $this->base_price);
        return $price * $hours;
    }

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }
}