<?php
// app/Models/Route.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'origin',
        'destination',
        'distance_km',
        'estimated_time',
        'status'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_time' => 'datetime:H:i:s',
        'status' => 'string'
    ];

    /**
     * Get the trips for the route.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Scope a query to only include active routes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'activa');
    }

    /**
     * Scope a query to only include inactive routes.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactiva');
    }

    /**
     * Scope a query to only include pending routes.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    /**
     * Get the route's full description.
     */
    public function getFullDescriptionAttribute()
    {
        return "{$this->origin} - {$this->destination} ({$this->distance_km} km)";
    }
}