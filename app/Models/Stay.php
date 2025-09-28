<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'checkIn',
        'checkOut',
        'status',
        'rateID',
        'roomID'
    ];

    protected $casts = [
        'checkIn' => 'datetime',
        'checkOut' => 'datetime',
    ];

    // Status constants
    const STATUS_STANDARD = 'Standard';
    const STATUS_EXTEND = 'Extend';

    // Get valid status values
    public static function getValidStatuses()
    {
        return [self::STATUS_STANDARD, self::STATUS_EXTEND];
    }

    // Relationships
    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rateID');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'roomID');
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'guest_stays', 'stayID', 'guestID')
                    ->withTimestamps();
    }

    public function guestStays()
    {
        return $this->hasMany(GuestStay::class, 'stayID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'stayID');
    }
}
