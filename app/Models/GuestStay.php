<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestStay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guestID',
        'stayID',
        'addressID'
    ];

    // Relationships
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guestID');
    }

    public function stay()
    {
        return $this->belongsTo(Stay::class, 'stayID');
    }

 
}

