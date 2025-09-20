<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination',
        'distance_km',
        'estimated_time',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con viajes
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // Obtener distancia formateada
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 2) . ' km';
    }

    // Obtener descripción de la ruta
    public function getDescriptionAttribute(): string
    {
        return "{$this->origin} → {$this->destination}";
    }
}