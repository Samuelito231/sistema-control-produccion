<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'requested_role',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved_at' => 'datetime',
    ];

    // Relación con el usuario que aprobó/rechazó esta cuenta
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope para usuarios pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para usuarios activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope para usuarios suspendidos
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
}