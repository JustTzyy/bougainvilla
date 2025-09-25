<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateAccommodation extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'rate_accommodations';

    protected $fillable = [
        'rate_id',
        'accommodation_id',
    ];
}






