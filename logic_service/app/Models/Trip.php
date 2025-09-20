<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'route_id',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'vehicle_id' => 'integer',
        'driver_id' => 'integer',
        'route_id' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Estados del viaje
    public const STATUS_PENDING = 'pendiente';
    public const STATUS_IN_PROGRESS = 'en_progreso';
    public const STATUS_COMPLETED = 'completado';
    public const STATUS_CANCELLED = 'cancelado';

    // Relación con vehículo
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relación con conductor
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    // Relación con ruta
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    // Relación con entregas
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    // Relación con incidentes
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    // Obtener todos los estados disponibles
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_IN_PROGRESS => 'En Progreso',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    // Verificar si el viaje está activo
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    // Verificar si el viaje está completado
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}