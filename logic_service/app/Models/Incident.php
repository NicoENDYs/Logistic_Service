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

    // Marcar como no resuelto
    public function markAsUnresolved(): void
    {
        $this->update(['resolved' => false]);
    }

    // Alternar estado de resolución
    public function toggleResolved(): bool
    {
        $newStatus = !$this->resolved;
        $this->update(['resolved' => $newStatus]);
        return $newStatus;
    }

    // Obtener incidentes por estado de resolución
    public static function byResolved(bool $resolved = true)
    {
        return static::where('resolved', $resolved);
    }

    // Obtener incidentes pendientes
    public static function pending()
    {
        return static::where('resolved', false);
    }

    // Obtener incidentes resueltos
    public static function resolved()
    {
        return static::where('resolved', true);
    }

    // Obtener incidentes por tipo
    public static function byType(string $type)
    {
        return static::where('type', $type);
    }

    // Obtener incidentes de un viaje
    public static function forTrip(int $tripId)
    {
        return static::where('trip_id', $tripId);
    }

    // Obtener incidentes con relaciones
    public static function withRelations()
    {
        return static::with(['trip.vehicle', 'trip.driver.user', 'trip.route']);
    }

    // Obtener información resumida del incidente
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'description' => substr($this->description, 0, 100) . (strlen($this->description) > 100 ? '...' : ''),
            'type' => $this->type,
            'vehicle' => $this->trip->vehicle->plate_number ?? 'N/A',
            'driver' => $this->trip->driver->user->name ?? 'N/A',
            'reported_at' => $this->reported_at,
            'resolved' => $this->resolved,
        ];
    }

    // Obtener resumen estadístico de incidentes
    public static function getStatsSummary(): array
    {
        return [
            'total' => static::count(),
            'resolved' => static::where('resolved', true)->count(),
            'pending' => static::where('resolved', false)->count(),
            'by_type' => [
                self::TYPE_ACCIDENT => static::where('type', self::TYPE_ACCIDENT)->count(),
                self::TYPE_DELAY => static::where('type', self::TYPE_DELAY)->count(),
                self::TYPE_MECHANICAL => static::where('type', self::TYPE_MECHANICAL)->count(),
                self::TYPE_OTHER => static::where('type', self::TYPE_OTHER)->count(),
            ]
        ];
    }

    // Scope para incidentes recientes
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('reported_at', '>=', now()->subDays($days));
    }
}