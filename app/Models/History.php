<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'userID'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id'); 
    }

    public function getActivityType()
    {
        $status = strtolower($this->status);
        
        if (strpos($status, 'created') !== false || strpos($status, 'added') !== false) {
            return 'Created';
        } elseif (strpos($status, 'updated') !== false || strpos($status, 'edited') !== false) {
            return 'Updated';
        } elseif (strpos($status, 'deleted') !== false || strpos($status, 'removed') !== false) {
            return 'Deleted';
        } elseif (strpos($status, 'restored') !== false) {
            return 'Restored';
        } elseif (strpos($status, 'login') !== false) {
            return 'Login';
        } elseif (strpos($status, 'logout') !== false) {
            return 'Logout';
        } else {
            return 'Action';
        }
    }

    public function getActivityBadgeClass()
    {
        $status = strtolower($this->status);
        
        if (strpos($status, 'created') !== false || strpos($status, 'added') !== false) {
            return 'badge-created';
        } elseif (strpos($status, 'updated') !== false || strpos($status, 'edited') !== false) {
            return 'badge-updated';
        } elseif (strpos($status, 'deleted') !== false || strpos($status, 'removed') !== false) {
            return 'badge-deleted';
        } elseif (strpos($status, 'restored') !== false) {
            return 'badge-restored';
        } elseif (strpos($status, 'login') !== false) {
            return 'badge-login';
        } elseif (strpos($status, 'logout') !== false) {
            return 'badge-logout';
        } else {
            return 'badge-created';
        }
    }
}
