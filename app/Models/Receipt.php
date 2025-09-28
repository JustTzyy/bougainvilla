<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'status_type',
        'paymentID',
        'userID'
    ];

    // Constants for status_type
    const STATUS_TYPE_STANDARD = 'Standard';
    const STATUS_TYPE_EXTEND = 'Extend';

    public static function getValidStatusTypes()
    {
        return [self::STATUS_TYPE_STANDARD, self::STATUS_TYPE_EXTEND];
    }

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'paymentID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}



