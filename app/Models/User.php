<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Permission checking methods
    public function hasPermission($permissionSlug)
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasPermission($permissionSlug);
    }

    public function hasAnyPermission($permissions)
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasAnyPermission($permissions);
    }

    // Convenience methods
    public function canViewUsers()
    {
        return $this->hasPermission('users.view');
    }

    public function canCreateUsers()
    {
        return $this->hasPermission('users.create');
    }

    public function canEditUsers()
    {
        return $this->hasPermission('users.edit');
    }

    public function canDeleteUsers()
    {
        return $this->hasPermission('users.delete');
    }

    public function canViewRoles()
    {
        return $this->hasPermission('roles.view');
    }

    public function canCreateRoles()
    {
        return $this->hasPermission('roles.create');
    }

    public function canEditRoles()
    {
        return $this->hasPermission('roles.edit');
    }

    public function canDeleteRoles()
    {
        return $this->hasPermission('roles.delete');
    }

    public function canViewTenants()
    {
        return $this->hasPermission('tenants.view');
    }

    public function canCreateTenants()
    {
        return $this->hasPermission('tenants.create');
    }

    public function canEditTenants()
    {
        return $this->hasPermission('tenants.edit');
    }

    public function canDeleteTenants()
    {
        return $this->hasPermission('tenants.delete');
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isManager()
    {
        return $this->role_id === 2;
    }

    public function isRegularUser()
    {
        return $this->role_id === 3;
    }

    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        return $query->where('tenant_id', $tenantId);
    }
}