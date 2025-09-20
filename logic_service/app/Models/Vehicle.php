<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
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

    // Verificar si el vehículo está activo
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}