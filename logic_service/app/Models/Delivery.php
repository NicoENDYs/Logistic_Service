<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'customer_name',
        'delivery_address',
        'delivery_time',
        'status',
    ];

    protected $casts = [
        'trip_id' => 'integer',
        'delivery_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Estados de la entrega
    public const STATUS_PENDING = 'pendiente';
    public const STATUS_DELIVERED = 'entregado';
    public const STATUS_FAILED = 'fallido';

    // Relación con viaje
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    // Obtener todos los estados disponibles
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_FAILED => 'Fallido',
        ];
    }

    // Verificar si la entrega está pendiente
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Verificar si la entrega fue exitosa
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    // Verificar si la entrega falló
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    // Marcar entrega como completada
    public function markAsDelivered(): bool
    {
        if ($this->isDelivered()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivery_time' => now(),
        ]);
    }

    // Marcar entrega como fallida
    public function markAsFailed(): bool
    {
        if ($this->isFailed()) {
            return false;
        }
        
        return $this->update(['status' => self::STATUS_FAILED]);
    }

    // Cambiar estado de la entrega
    public function changeStatus(string $newStatus): bool
    {
        if (!in_array($newStatus, array_keys(self::getStatuses()))) {
            return false;
        }
        
        $updateData = ['status' => $newStatus];
        
        if ($newStatus === self::STATUS_DELIVERED && !$this->delivery_time) {
            $updateData['delivery_time'] = now();
        }
        
        return $this->update($updateData);
    }

    // Obtener entregas por estado
    public static function byStatus(string $status)
    {
        return static::where('status', $status);
    }

    // Obtener entregas de un viaje
    public static function forTrip(int $tripId)
    {
        return static::where('trip_id', $tripId);
    }

    // Obtener entregas con relaciones
    public static function withRelations()
    {
        return static::with(['trip.vehicle', 'trip.driver.user', 'trip.route']);
    }

    // Obtener información resumida de la entrega
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'delivery_address' => $this->delivery_address,
            'vehicle' => $this->trip->vehicle->plate_number ?? 'N/A',
            'driver' => $this->trip->driver->user->name ?? 'N/A',
            'status' => $this->status,
            'delivery_time' => $this->delivery_time,
        ];
    }

    // Verificar si se puede eliminar la entrega
    public function canBeDeleted(): bool
    {
        return $this->status !== self::STATUS_DELIVERED;
    }
}