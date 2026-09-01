<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalkInPatient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'recorded_by_id',
        'first_name',
        'middle_initial',
        'last_name',
        'date_of_birth',
        'address',
        'barangay',
        'purok_id',
        'contact_number',
        'reason_for_visit',
        'notes',
        'converted_to_user_id',
        'converted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'converted_at' => 'datetime',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    public function purok(): BelongsTo
    {
        return $this->belongsTo(Purok::class, 'purok_id');
    }

    public function convertedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_to_user_id');
    }

    public function checkupReferrals(): HasMany
    {
        return $this->hasMany(CheckupReferral::class, 'walk_in_patient_id');
    }

    public function pregnancies(): HasMany
    {
        return $this->hasMany(Pregnancy::class, 'walk_in_patient_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_initial} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }

    public function scopeNotConverted($query)
    {
        return $query->whereNull('converted_to_user_id');
    }

    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_to_user_id');
    }
}
