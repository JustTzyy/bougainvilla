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

    /**
     * Calculate tax for this receipt
     * This is where tax deduction calculation is performed
     */
    public function calculateTax($taxRate = 0.12)
    {
        if (!$this->payment) {
            return 0;
        }
        
        // Tax is calculated on the payment amount (which is the rate price)
        return $this->payment->amount * $taxRate;
    }

    /**
     * Get the total amount including tax
     */
    public function getTotalWithTax($taxRate = 0.12)
    {
        if (!$this->payment) {
            return 0;
        }
        
        return $this->payment->amount + $this->calculateTax($taxRate);
    }

    /**
     * Get the subtotal (amount before tax)
     */
    public function getSubtotal()
    {
        return $this->payment ? $this->payment->amount : 0;
    }
}



