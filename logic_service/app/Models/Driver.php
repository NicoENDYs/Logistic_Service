<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'phone',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Estados del conductor
    public const STATUS_ACTIVE = 'activo';
    public const STATUS_SUSPENDED = 'suspendido';

    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con viajes
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // Obtener todos los estados disponibles
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_SUSPENDED => 'Suspendido',
        ];
    }

    // Verificar si el conductor está activo
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Obtener nombre completo del conductor
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Sin nombre';
    }
}