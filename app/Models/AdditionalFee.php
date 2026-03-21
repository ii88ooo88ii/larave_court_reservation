<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'pricing_id',
        'name',
        'type',
        'amount',
        'is_mandatory',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function pricing()
    {
        return $this->belongsTo(Pricing::class);
    }

    public function calculateFee($subtotal)
    {
        if ($this->type === 'percentage') {
            return $subtotal * ($this->amount / 100);
        }
        return $this->amount;
    }

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }
}