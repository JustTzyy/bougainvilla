<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'amount',
        'tax',
        'subtotal',
        'status',
        'change',
        'stayID'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    // Relationships
    public function stay()
    {
        return $this->belongsTo(Stay::class, 'stayID');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'paymentID');
    }
}



