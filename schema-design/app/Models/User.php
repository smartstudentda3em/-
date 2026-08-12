<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    /* ------------------- العلاقات ------------------- */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class); // محور role_user
    }

    public function booklets(): HasMany
    {
        return $this->hasMany(Booklet::class, 'uploaded_by');
    }

    public function requestedOrders(): HasMany
    {
        return $this->hasMany(PrintOrder::class, 'requested_by');
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(PrintOrder::class, 'assigned_to');
    }

    /* ------------------- مساعدات الصلاحيات ------------------- */

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles
            ->loadMissing('permissions')
            ->flatMap->permissions
            ->contains('name', $permission);
    }
}
