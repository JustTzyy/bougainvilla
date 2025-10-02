<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['duration','price','status'];

    // Rate status constants
    const STATUS_STANDARD = 'Standard';
    const STATUS_EXTENDING = 'Extending';
    const STATUS_EXTENDING_STANDARD = 'Extending/Standard';

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'rate_accommodations')
                    ->withTimestamps()
                    ->withPivot('id')
                    ->using(\App\Models\RateAccommodation::class);
    }

    /**
     * Get all valid rate statuses
     */
    public static function getValidStatuses()
    {
        return [
            self::STATUS_STANDARD,
            self::STATUS_EXTENDING,
            self::STATUS_EXTENDING_STANDARD
        ];
    }

    /**
     * Check if the rate is available for booking
     */
    public function isAvailable()
    {
        return in_array($this->status, self::getValidStatuses());
    }
}
