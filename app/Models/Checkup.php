<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Checkup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'walk_in_patient_id',
        'midwife_id',
        'scheduled_by_id',
        'bhw_president_id',
        'scheduled_date',
        'scheduled_time',
        'actual_date',
        'purpose',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    // Relationships

    /**
     * The woman for this checkup
     */
    public function woman()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walkInPatient()
    {
        return $this->belongsTo(WalkInPatient::class, 'walk_in_patient_id');
    }

    /**
     * The assigned midwife for this checkup
     */
    public function midwife()
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }

    /**
     * The user (BHW or midwife) who scheduled this checkup
     */
    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by_id');
    }

    /**
     * Alias for scheduledBy - backward compatibility
     */
    public function scheduledByBhw()
    {
        return $this->scheduledBy();
    }

    /**
     * The BHW President for this checkup
     */
    public function bhwPresident()
    {
        return $this->belongsTo(User::class, 'bhw_president_id');
    }

    // Alias for backward compatibility
    public function user()
    {
        return $this->woman();
    }

    public function patient()
    {
        return $this->woman();
    }

    public function getPatientRecordAttribute()
    {
        return $this->woman ?? $this->walkInPatient;
    }

    public function getPatientNameAttribute(): string
    {
        return $this->woman?->name
            ?? $this->walkInPatient?->full_name
            ?? 'Unknown patient';
    }

    public function midwifeUser()
    {
        return $this->midwife();
    }

    /**
     * The user (BHW or midwife) who scheduled this checkup
     */
    public function getScheduledByAttribute()
    {
        return $this->scheduledBy();
    }

    /**
     * Get the assigned midwife
     */
    public function getAssignedMidwifeAttribute()
    {
        return $this->midwife;
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeMissed($query)
    {
        return $query->where('status', 'Missed');
    }

    public function scopeRescheduled($query)
    {
        return $query->where('status', 'Rescheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', Carbon::now())
                    ->where('status', 'Scheduled');
    }

    // Methods
    public function markAsMissed()
    {
        if ($this->scheduled_date < Carbon::now() && $this->status === 'Scheduled') {
            $this->status = 'Missed';
            $this->save();

            // Load user relation for notifications
            $this->loadMissing('user');

            // Use SmartNotificationService for full notifications
            $notificationService = new \App\Services\SmartNotificationService();
            $notificationService->notifyMissedCheckup($this);

            // Also trigger risk analysis
            $riskService = new \App\Services\RiskAnalysisService();
            $riskService->evaluate($this->user_id);
        }
    }

    public static function markOverdueCheckups()
    {
        self::overdue()->with('user')->get()->each(function ($checkup) {
            $checkup->markAsMissed();
        });
    }
}
