<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    // Scope to filter by tenant
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }

    // Get available icons list
    public static function getAvailableIcons()
    {
        return [
            'fa-basketball-ball' => 'Basketball',
            'fa-table-tennis' => 'Table Tennis',
            'fa-tennis-ball' => 'Tennis',
            'fa-volleyball-ball' => 'Volleyball',
            'fa-futbol' => 'Soccer',
            'fa-baseball-ball' => 'Baseball',
            'fa-golf-ball' => 'Golf',
            'fa-hockey-puck' => 'Hockey',
            'fa-dumbbell' => 'Gym',
            'fa-swimmer' => 'Swimming',
            'fa-running' => 'Running',
            'fa-bicycle' => 'Cycling',
            'fa-pickleball' => 'Pickleball',
            'fa-badminton' => 'Badminton',
            'fa-squash' => 'Squash',
        ];
    }
}