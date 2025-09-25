<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['duration','price','status'];

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'rate_accommodations')
                    ->withTimestamps()
                    ->withPivot('id')
                    ->using(\App\Models\RateAccommodation::class);
    }
}
