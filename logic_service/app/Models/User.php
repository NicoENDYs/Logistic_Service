<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
        ];
    }

    // Relación con rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relación con conductor (si aplica)
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    // Verificar si es administrador
    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    // Verificar si es gestor
    public function isManager(): bool
    {
        return $this->role && $this->role->isManager();
    }

    // Verificar si es chofer
    public function isDriver(): bool
    {
        return $this->role && $this->role->isDriver();
    }

    // Obtener nombre del rol
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : 'Sin rol';
    }
}
