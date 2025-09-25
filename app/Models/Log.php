<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['timeIn', 'timeOut', 'status', 'userID'];

    protected $casts = [
        'timeIn' => 'datetime',
        'timeOut' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function getActivityType()
    {
        $status = strtolower($this->status);
        
        if (strpos($status, 'login') !== false) {
            return 'Login';
        } elseif (strpos($status, 'logout') !== false) {
            return 'Logout';
        } else {
            return 'Activity';
        }
    }

    public function getActivityBadgeClass()
    {
        $status = strtolower($this->status);
        
        if (strpos($status, 'login') !== false) {
            return 'badge-login';
        } elseif (strpos($status, 'logout') !== false) {
            return 'badge-logout';
        } else {
            return 'badge-login';
        }
    }
}
