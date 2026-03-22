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

    // ... rest of the model
}