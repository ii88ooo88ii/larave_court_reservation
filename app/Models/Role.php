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

    public static function getRolesList()
    {
        return [
            1 => 'Admin',
            2 => 'Manager',
            3 => 'Regular User',
        ];
    }
}