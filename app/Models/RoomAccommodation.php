<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomAccommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'accommodation_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
