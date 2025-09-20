<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'description',
        'type',
        'reported_at',
        'resolved',
    ];

    protected $casts = [
        'trip_id' => 'integer',
        'reported_at' => 'datetime',
        'resolved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de incidente
    public const TYPE_ACCIDENT = 'accidente';
    public const TYPE_DELAY = 'retraso';
    public const TYPE_MECHANICAL = 'mecanico';
    public const TYPE_OTHER = 'otro';

    // Relación con viaje
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    // Obtener todos los tipos disponibles
    public static function getTypes(): array
    {
        return [
            self::TYPE_ACCIDENT => 'Accidente',
            self::TYPE_DELAY => 'Retraso',
            self::TYPE_MECHANICAL => 'Mecánico',
            self::TYPE_OTHER => 'Otro',
        ];
    }

    // Verificar si el incidente está resuelto
    public function isResolved(): bool
    {
        return $this->resolved === true;
    }

    // Marcar como resuelto
    public function markAsResolved(): void
    {
        $this->update(['resolved' => true]);
    }
}