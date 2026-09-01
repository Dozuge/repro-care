<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Checkup;
use App\Services\SmsService;

class SmartNotificationService
{
    /**
     * Notify patient AND their assigned health workers about detected risk.
     */
    public function notifyHighRisk(User $patient, string $reason, string $riskLevel = 'High'): void
    {
        $typeMap = ['High' => 'danger', 'Medium' => 'warning', 'Low' => 'info'];
        $type    = $typeMap[$riskLevel] ?? 'warning';
        $emoji   = $riskLevel === 'High' ? '🚨' : '⚠️';

        // Notify patient
        $this->create(
            $patient->id,
            'user',
            "{$emoji} {$riskLevel} Risk Alert",
            "Our system has detected a health concern: {$reason} Please contact your health worker immediately.",
            $type,
            '/user/health-records'
        );

        // ── SMS Alert to patient ──────────────────────────────────────
        try {
            (new SmsService())->send($patient, 'high_risk_alert', ['reason' => $reason]);
        } catch (\Throwable $e) {
            // Never let SMS failure crash the main flow
        }

        // Notify their assigned BHW (last BHW who recorded a health record)
        $assignedBhw = $patient->healthRecords()
            ->with('recordedBy')
            ->whereNotNull('recorded_by_id')
            ->latest()
            ->first()?->recordedBy;

        if ($assignedBhw) {
            $this->create(
                $assignedBhw->id,
                'bhw',
                "{$emoji} Patient Risk Alert – {$patient->name}",
                "{$riskLevel} risk detected for {$patient->name}: {$reason}",
                $type,
                "/bhw/patients/{$patient->id}"
            );
        }

        // Notify midwife (from latest checkup's midwife_id)
        $midwifeId = $patient->checkups()
            ->whereNotNull('midwife_id')
            ->latest()
            ->value('midwife_id');

        if ($midwifeId) {
            $this->create(
                $midwifeId,
                'midwife',
                "{$emoji} Patient Risk Alert – {$patient->name}",
                "{$riskLevel} risk detected for {$patient->name}: {$reason}",
                $type,
                "/midwife/patients/{$patient->id}"
            );
        }
    }

    /**
     * Notify patient and BHW/midwife about a missed checkup.
     */
    public function notifyMissedCheckup(Checkup $checkup): void
    {
        $date = $checkup->scheduled_date->format('F j, Y');

        // Patient notification
        $this->create(
            $checkup->user_id,
            'user',
            '⛔ Missed Checkup',
            "You missed your scheduled checkup on {$date}. Please reschedule as soon as possible.",
            'danger',
            '/user/checkups'
        );

        // ── SMS Alert to patient ──────────────────────────────────────
        if ($checkup->user_id && $checkup->woman) {
            try {
                (new SmsService())->send($checkup->woman, 'missed_checkup', ['date' => $date]);
            } catch (\Throwable $e) {
                // Never let SMS failure crash the main flow
            }
        }

        // BHW who scheduled it
        if ($checkup->scheduled_by_id) {
            $this->create(
                $checkup->scheduled_by_id,
                'bhw',
                '⛔ Checkup Missed – ' . optional($checkup->woman)->name,
                "The checkup scheduled on {$date} for " . optional($checkup->woman)->name . " was missed.",
                'warning',
                "/bhw/patients/{$checkup->user_id}"
            );
        } elseif ($checkup->scheduled_by_midwife_id) {
            $this->create(
                $checkup->scheduled_by_midwife_id,
                'midwife',
                '⛔ Checkup Missed – ' . optional($checkup->woman)->name,
                "The checkup scheduled on {$date} for " . optional($checkup->woman)->name . " was missed.",
                'warning',
                "/midwife/patients/{$checkup->user_id}"
            );
        }

        // Assigned midwife
        if ($checkup->midwife_id) {
            $this->create(
                $checkup->midwife_id,
                'midwife',
                '⛔ Checkup Missed – ' . optional($checkup->woman)->name,
                "Checkup on {$date} for " . optional($checkup->woman)->name . " was missed.",
                'warning',
                "/midwife/patients/{$checkup->user_id}"
            );
        }
    }

    /**
     * Remind patient about upcoming checkup (call 1 day before).
     */
    public function notifyUpcomingCheckup(Checkup $checkup): void
    {
        $date = $checkup->scheduled_date->format('F j, Y');
        $time = $checkup->scheduled_time
            ? \Carbon\Carbon::parse($checkup->scheduled_time)->format('h:i A')
            : 'your scheduled time';

        $this->create(
            $checkup->user_id,
            'user',
            '📅 Upcoming Checkup Reminder',
            "You have a checkup scheduled for tomorrow, {$date}. Please do not miss it.",
            'info',
            '/user/checkups'
        );

        // ── SMS Reminder to patient ───────────────────────────────────
        if ($checkup->user_id && $checkup->woman) {
            try {
                (new SmsService())->send($checkup->woman, 'appointment_reminder', [
                    'date' => $date,
                    'time' => $time,
                ]);
            } catch (\Throwable $e) {
                // Never let SMS failure crash the main flow
            }
        }
    }

    /**
     * Send a generic notification.
     */
    private function create(int $userId, string $userRole, string $title, string $message, string $type = 'info', ?string $actionUrl = null): void
    {
        $data = [
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'action_url' => $actionUrl,
            'is_read'    => false,
        ];

        if ($userRole === 'user') {
            $data['user_id'] = $userId;
        } elseif ($userRole === 'midwife') {
            $data['midwife_id'] = $userId;
        } elseif ($userRole === 'bhw') {
            $data['bhw_id'] = $userId;
        }

        Notification::create($data);
    }
}
