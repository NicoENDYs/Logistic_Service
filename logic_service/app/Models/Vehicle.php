<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Estados del vehículo
    public const STATUS_ACTIVE = 'activo';
    public const STATUS_INACTIVE = 'inactivo';
    public const STATUS_MAINTENANCE = 'mantenimiento';

    // Crear vehículo con validación
    public static function createVehicle(array $data): Vehicle
    {
        $validator = Validator::make($data, [
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'status' => ['nullable', 'string', 'in:activo,inactivo,mantenimiento'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data['status'] = $data['status'] ?? self::STATUS_ACTIVE;
        return self::create($data);
    }

    // Crear vehículo rápido con parámetros directos
    public static function quickCreate(
        string $plateNumber,
        string $brand,
        string $model,
        int $capacity,
        string $status = self::STATUS_ACTIVE
    ): Vehicle {
        return self::createVehicle([
            'plate_number' => $plateNumber,
            'brand' => $brand,
            'model' => $model,
            'capacity' => $capacity,
            'status' => $status,
        ]);
    }

    // Obtener todos los estados disponibles
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_INACTIVE => 'Inactivo',
            self::STATUS_MAINTENANCE => 'En Mantenimiento',
        ];
    }

    // Relación con viajes
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // Verificar si el vehículo está activo
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Verificar si el vehículo está disponible para viajes
    public function isAvailable(): bool
    {
        return $this->isActive() && !$this->hasActiveTrips();
    }

    // Verificar si tiene viajes activos
    public function hasActiveTrips(): bool
    {
        return $this->trips()->whereIn('status', ['pendiente', 'en_progreso'])->exists();
    }

    // Obtener vehículos activos
    public static function active()
    {
        return static::where('status', self::STATUS_ACTIVE);
    }

    // Obtener vehículos disponibles
    public static function available()
    {
        return static::active()->whereDoesntHave('trips', function($query) {
            $query->whereIn('status', ['pendiente', 'en_progreso']);
        });
    }

    // Obtener vehículos por estado
    public static function byStatus(string $status)
    {
        return static::where('status', $status);
    }

    // Cambiar estado del vehículo
    public function changeStatus(string $newStatus): bool
    {
        if (!in_array($newStatus, [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_MAINTENANCE])) {
            return false;
        }
        
        return $this->update(['status' => $newStatus]);
    }

    // Alternar estado entre activo/inactivo
    public function toggleStatus(): string
    {
        $newStatus = match($this->status) {
            self::STATUS_ACTIVE => self::STATUS_INACTIVE,
            self::STATUS_INACTIVE => self::STATUS_ACTIVE,
            self::STATUS_MAINTENANCE => self::STATUS_ACTIVE,
            default => self::STATUS_ACTIVE
        };
        
        $this->update(['status' => $newStatus]);
        return $newStatus;
    }

    // Obtener información resumida del vehículo
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'plate_number' => $this->plate_number,
            'brand' => $this->brand,
            'model' => $this->model,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'is_available' => $this->isAvailable(),
        ];
    }

    // Obtener nombre completo del vehículo
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->plate_number})";
    }
}