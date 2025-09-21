<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room',
        'status',
        'type',
        'level_id',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'room_accommodations')
                    ->withTimestamps()
                    ->withPivot('id');
    }

    public function roomAccommodations()
    {
        return $this->hasMany(RoomAccommodation::class);
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for filtering by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope for active rooms (not archived)
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'Under Maintenance');
    }

    // Scope for archived rooms (under maintenance)
    public function scopeArchived($query)
    {
        return $query->where('status', 'Under Maintenance');
    }
}
