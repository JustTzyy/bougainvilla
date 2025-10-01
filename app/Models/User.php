<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['firstName','middleName','lastName','email','name','password','password_changed','contactNumber','roleID','email_verified_at','birthday','age','sex','status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleID', 'id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'userID', 'id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'userID');
    }
}
