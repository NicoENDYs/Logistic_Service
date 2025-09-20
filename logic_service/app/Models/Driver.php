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

    // Verificar si el conductor está disponible para viajes
    public function isAvailable(): bool
    {
        return $this->isActive() && !$this->hasActiveTrips();
    }

    // Verificar si tiene viajes activos
    public function hasActiveTrips(): bool
    {
        return $this->trips()->whereIn('status', ['pendiente', 'en_progreso'])->exists();
    }

    // Obtener conductores activos
    public static function active()
    {
        return static::where('status', self::STATUS_ACTIVE);
    }

    // Obtener conductores disponibles
    public static function available()
    {
        return static::active()->whereDoesntHave('trips', function($query) {
            $query->whereIn('status', ['pendiente', 'en_progreso']);
        });
    }

    // Cambiar estado del conductor
    public function changeStatus(string $newStatus): bool
    {
        if (!in_array($newStatus, [self::STATUS_ACTIVE, self::STATUS_SUSPENDED])) {
            return false;
        }
        
        return $this->update(['status' => $newStatus]);
    }

    // Alternar estado entre activo/suspendido
    public function toggleStatus(): string
    {
        $newStatus = $this->status === self::STATUS_ACTIVE ? self::STATUS_SUSPENDED : self::STATUS_ACTIVE;
        $this->update(['status' => $newStatus]);
        return $newStatus;
    }

    // Obtener información resumida del conductor
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'license_number' => $this->license_number,
            'phone' => $this->phone,
            'status' => $this->status,
            'is_available' => $this->isAvailable(),
        ];
    }

    // Obtener conductores con usuarios
    public static function withUsers()
    {
        return static::with('user');
    }

    // Scope para conductores sin viajes activos
    public function scopeWithoutActiveTrips($query)
    {
        return $query->whereDoesntHave('trips', function($q) {
            $q->whereIn('status', ['pendiente', 'en_progreso']);
        });
    }
}