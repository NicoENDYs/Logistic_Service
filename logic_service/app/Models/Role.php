<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Roles del sistema
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'gestor';
    public const ROLE_DRIVER = 'chofer';

    // Relación con usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Obtener todos los roles disponibles
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_MANAGER => 'Gestor',
            self::ROLE_DRIVER => 'Chofer',
        ];
    }

    // Verificar si es rol de administrador
    public function isAdmin(): bool
    {
        return $this->name === self::ROLE_ADMIN;
    }

    // Verificar si es rol de gestor
    public function isManager(): bool
    {
        return $this->name === self::ROLE_MANAGER;
    }

    // Verificar si es rol de chofer
    public function isDriver(): bool
    {
        return $this->name === self::ROLE_DRIVER;
    }
}