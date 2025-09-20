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
}