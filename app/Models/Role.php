<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    // Check if role has specific permission
    public function hasPermission($permissionSlug)
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    // Check if role has any of the given permissions
    public function hasAnyPermission($permissions)
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }

    // Sync permissions
    public function syncPermissions(array $permissionIds)
    {
        $this->permissions()->sync($permissionIds);
    }
}