<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckupReferral extends Model
{
    protected $fillable = [
        'referred_by_bhw_id',
        'user_id',
        'walk_in_patient_id',
        'assigned_midwife_id',
        'converted_checkup_id',
        'reason',
        'urgency',
        'bhw_notes',
        'midwife_notes',
        'status',
        'reviewed_at',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function referredByBhw(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_bhw_id');
    }

    public function woman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walkInPatient(): BelongsTo
    {
        return $this->belongsTo(WalkInPatient::class, 'walk_in_patient_id');
    }

    public function assignedMidwife(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_midwife_id');
    }

    public function convertedCheckup(): BelongsTo
    {
        return $this->belongsTo(Checkup::class, 'converted_checkup_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['urgent', 'emergency']);
    }

    public function getPatientNameAttribute(): string
    {
        if ($this->woman) {
            return $this->woman->name;
        }
        if ($this->walkInPatient) {
            return $this->walkInPatient->full_name;
        }
        return 'Unknown';
    }
}
