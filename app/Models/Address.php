<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
      use HasFactory, SoftDeletes;

    protected $fillable = ['street','city','province','zipcode','userID',];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'addressID');
    }

    public function guestStays()
    {
        return $this->hasMany(GuestStay::class, 'addressID');
    }
}
