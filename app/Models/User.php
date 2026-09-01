<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'contact_number',
        'address',
        'barangay',
        'purok_id',
        'assigned_barangay',
        'role',
        'status',
        'profile_image',
        'rejection_reason',
        'rhu_assignment',
        'cho_office',
        'registered_by_rhu_id',
        'registered_by_cho_id',
        'sms_opt_out',
        'partner_name',
        'partner_contact',
    ];

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
            'password'          => 'hashed',
            'date_of_birth'     => 'date',
        ];
    }

    // ── Domain Relationships ─────────────────────────────────────────────────

    public function pregnancies()
    {
        return $this->hasMany(Pregnancy::class, 'user_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class, 'user_id')->ordered();
    }

    public function primaryEmergencyContact()
    {
        return $this->hasOne(EmergencyContact::class, 'user_id')->where('contact_order', 1);
    }

    public function secondaryEmergencyContact()
    {
        return $this->hasOne(EmergencyContact::class, 'user_id')->where('contact_order', 2);
    }

    public function tertiaryEmergencyContact()
    {
        return $this->hasOne(EmergencyContact::class, 'user_id')->where('contact_order', 3);
    }

    public function maternalCareTargetClients()
    {
        return $this->hasMany(MaternalCareTargetClient::class, 'user_id');
    }

    // Alias for backward compatibility - returns the most recent record
    public function maternalCareTargetClient()
    {
        return $this->hasOne(MaternalCareTargetClient::class, 'user_id')->latest();
    }

    public function childRecords()
    {
        return $this->hasMany(ChildRecord::class, 'mother_id');
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    public function bhwAssignments()
    {
        return $this->hasMany(BhwAssignment::class, 'bhw_id');
    }

    public function activeBhwAssignment()
    {
        return $this->hasOne(BhwAssignment::class, 'bhw_id')->where('is_active', true)->latestOfMany();
    }

    public function cycles()
    {
        return $this->hasMany(Cycle::class, 'user_id');
    }


    public function checkups()
    {
        if ($this->role === 'user') {
            return $this->hasMany(Checkup::class, 'user_id');
        }
        return $this->hasMany(Checkup::class, 'user_id')->whereRaw('1=0');
    }

    public function healthRecords()
    {
        if ($this->role === 'user') {
            return $this->hasMany(HealthRecord::class, 'user_id');
        } elseif ($this->role === 'midwife') {
            return $this->hasMany(HealthRecord::class, 'recorded_by_id');
        } elseif ($this->role === 'bhw') {
            return $this->hasMany(HealthRecord::class, 'recorded_by_id');
        }
        // Default fallback - shouldn't happen but return empty relation
        return $this->hasMany(HealthRecord::class, 'user_id')->whereRaw('1=0');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    public function forumLikes()
    {
        return $this->hasMany(ForumLike::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function recordedHealthRecords()
    {
        if ($this->role === 'midwife') {
            return $this->hasMany(HealthRecord::class, 'recorded_by_id');
        } elseif ($this->role === 'bhw') {
            return $this->hasMany(HealthRecord::class, 'recorded_by_id');
        }
        return $this->hasMany(HealthRecord::class, 'recorded_by_id')->whereRaw('1=0');
    }

    // Helper methods

    /**
     * Returns true if the user can receive SMS alerts.
     * Patient must have a contact number and must NOT have opted out.
     */
    public function hasSmsEnabled(): bool
    {
        return !empty($this->contact_number) && !$this->sms_opt_out;
    }

    public function isAdmin()
    {
        return $this->role === 'rhu' || $this->role === 'cho';
    }

    public function isCho()
    {
        return $this->role === 'cho';
    }

    public function isRhu()
    {
        return $this->role === 'rhu';
    }

    public function isMidwife()
    {
        return $this->role === 'midwife';
    }

    public function isBhw()
    {
        return $this->role === 'bhw';
    }

    public function isBhwPresident()
    {
        return $this->role === 'bhw_president';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isPending()
    {
        return ($this->status ?? 'approved') === 'pending';
    }

    public function isApproved()
    {
        return ($this->status ?? 'approved') === 'approved';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessors for Reports
    public function getNameAttribute()
    {
        $name = trim($this->first_name . ' ' . 
            ($this->middle_initial ? $this->middle_initial . '. ' : '') . 
            $this->last_name);
        return $name;
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return 'N/A';
        }
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getPregnancyStatusAttribute()
    {
        $activePregnancy = $this->pregnancies()->active()->first();
        if ($activePregnancy) {
            return 'Pregnant';
        }

        $completedPregnancy = $this->pregnancies()->completed()->first();
        if ($completedPregnancy) {
            return 'Postpartum';
        }

        return 'Not Pregnant';
    }

    public function getAssignedBhwAttribute()
    {
        $lastHealthRecord = $this->healthRecords()
            ->byBhw()
            ->latest()
            ->first();

        if ($lastHealthRecord) {
            return $lastHealthRecord->recordedBy;
        }

        return null;
    }

    public function getLastCheckupAttribute()
    {
        return $this->checkups()
            ->where('status', 'Completed')
            ->latest('scheduled_date')
            ->first();
    }

    public function getNextAppointmentAttribute()
    {
        return $this->checkups()
            ->where('status', 'Scheduled')
            ->where('scheduled_date', '>=', Carbon::now())
            ->orderBy('scheduled_date', 'asc')
            ->first();
    }

    // Profile Image Handling
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            $publicDisk = Storage::disk('public');
            $filename = basename($this->profile_image);

            if ($publicDisk->exists($this->profile_image)) {
                return '/storage/' . ltrim($this->profile_image, '/');
            }

            foreach (['uploads/profile/', 'profile/'] as $directory) {
                $path = $directory . $filename;

                if ($publicDisk->exists($path)) {
                    return '/storage/' . ltrim($path, '/');
                }
            }

            foreach (['uploads/profile/', 'profile/'] as $directory) {
                $legacyPublicPath = public_path($directory . $filename);

                if (file_exists($legacyPublicPath)) {
                    return '/' . trim(str_replace('\\', '/', $directory . $filename), '/');
                }
            }
        }

        // Return default avatar based on gender
        $defaultAvatar = $this->gender === 'male' ? 'avatar-male.svg' : 'avatar-female.svg';
        return '/images/avatars/' . $defaultAvatar;
    }

    public function hasProfileImage()
    {
        return !empty($this->profile_image);
    }
}
