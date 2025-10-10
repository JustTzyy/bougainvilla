<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'request_type',
        'request_data',
        'current_data',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes'
    ];

    protected $casts = [
        'request_data' => 'array',
        'current_data' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Constants for request types
    const TYPE_PERSONAL_INFO = 'personal_info';
    const TYPE_EMAIL = 'email';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
}
