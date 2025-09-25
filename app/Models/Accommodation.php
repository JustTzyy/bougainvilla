<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','capacity','description',];

    public function rates()
    {
        return $this->belongsToMany(Rate::class, 'rate_accommodations')
                    ->withTimestamps()
                    ->withPivot('id')
                    ->using(\App\Models\RateAccommodation::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_accommodations')
                    ->withTimestamps()
                    ->withPivot('id');
    }

    public function roomAccommodations()
    {
        return $this->hasMany(RoomAccommodation::class);
    }
}
