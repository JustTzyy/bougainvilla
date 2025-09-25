<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firstName',
        'middleName',
        'lastName',
        'number',
        'addressID',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'addressID');
    }

    public function stays()
    {
        return $this->belongsToMany(Stay::class, 'guest_stays', 'guestID', 'stayID')
                    ->withTimestamps();
    }
}


