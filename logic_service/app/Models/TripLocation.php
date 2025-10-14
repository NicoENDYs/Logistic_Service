<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLocation extends Model
{
    protected $table = 'trip_locations'; 

    protected $fillable=[
        'trip_id',
        'latitude',
        'longitude',
        'recorded_at'
    ];
    public function trip(){
        return $this->BelongsTo(Trip::class);
    }
}