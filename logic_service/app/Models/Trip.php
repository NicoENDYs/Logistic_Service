<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

     public static function Asign(array $data){
        $conflict = self::Where(function($query) use ($data) {
            $query->where('driver_id', $data['driver_id'])
            ->orwhere('vechicle_id', $data['vehicle_id']);
        })
        ->whereIn('status',['pendiente','en_progreso'])
        ->where(function($query) use ($data){
            $query->whereBetween('started_at',$data['started_at'],$data['ended_at'])
            -> orWhereBetween('ended_at',$data['started_at'], $data['ended_at']);

        })
        ->exists();
        
        if($conflict){
            throw new \Exception('Vehiculo o conductor no disponibles en el horario');
        }
        // si no hay conflicto de horario, cambia estado
        $data['status']='pendiente';

        return self::create($data);
        }
        
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

    // Verificar si se puede iniciar el viaje
    public function canStart(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Verificar si se puede completar el viaje
    public function canComplete(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    // Iniciar viaje
    public function start(): bool
    {
        if (!$this->canStart()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'start_time' => now(),
        ]);
    }

    // Completar viaje
    public function complete(): bool
    {
        if (!$this->canComplete()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_time' => now(),
        ]);
    }

    // Cancelar viaje
    public function cancel(): bool
    {
        if ($this->isCompleted()) {
            return false;
        }
        
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    // Cambiar estado del viaje con validaciones
    public function changeStatus(string $newStatus): bool
    {
        if (!in_array($newStatus, array_keys(self::getStatuses()))) {
            return false;
        }
        
        $updateData = ['status' => $newStatus];
        
        if ($newStatus === self::STATUS_IN_PROGRESS && !$this->start_time) {
            $updateData['start_time'] = now();
        } elseif ($newStatus === self::STATUS_COMPLETED && !$this->end_time) {
            $updateData['end_time'] = now();
        }
        
        return $this->update($updateData);
    }

    // Obtener viajes por estado
    public static function byStatus(string $status)
    {
        return static::where('status', $status);
    }

    // Obtener viajes activos
    public static function active()
    {
        return static::whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    // Obtener viajes con todas las relaciones
    public static function withAllRelations()
    {
        return static::with(['vehicle', 'driver.user', 'route', 'deliveries', 'incidents']);
    }

    // Obtener información resumida del viaje
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'vehicle' => $this->vehicle->plate_number ?? 'N/A',
            'driver' => $this->driver->user->name ?? 'N/A',
            'route' => $this->route->description ?? 'N/A',
            'status' => $this->status,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }

    // Scope para viajes de un conductor específico
    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    // Scope para viajes de un vehículo específico
    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // Verificar si se puede eliminar el viaje
    public function canBeDeleted(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CANCELLED]);
    }

   


    }
