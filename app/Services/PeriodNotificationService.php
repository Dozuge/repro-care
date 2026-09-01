<?php

namespace App\Services;

use App\Models\Cycle;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class PeriodNotificationService
{
    /**
     * Check for upcoming periods and create notifications
     *
     * @return array
     */
    public function checkAndNotify(): array
    {
        $notified = [];
        $today = now();

        // Get all users with cycles (role = 'user')
        $users = User::where('role', 'user')->whereHas('cycles')->get();

        foreach ($users as $user) {
            $nextPeriod = Cycle::predictNextPeriod($user->id);

            if (!$nextPeriod) {
                continue;
            }

            $daysUntil = $today->diffInDays($nextPeriod, false);

            // Notify 3 days before period
            if ($daysUntil == 3) {
                $this->createNotification(
                    $user->id,
                    'user',
                    'Period Reminder',
                    "Your period is expected in 3 days ({$nextPeriod->format('F j, Y')}).",
                    'period_upcoming',
                    $nextPeriod->format('Y-m-d')
                );
                $notified[] = ['user_id' => $user->id, 'type' => '3_days_before', 'date' => $nextPeriod->format('Y-m-d')];
            }

            // Notify 1 day before period
            if ($daysUntil == 1) {
                $this->createNotification(
                    $user->id,
                    'user',
                    'Period Tomorrow',
                    "Your period is expected tomorrow ({$nextPeriod->format('F j, Y')}).",
                    'period_tomorrow',
                    $nextPeriod->format('Y-m-d')
                );
                $notified[] = ['user_id' => $user->id, 'type' => '1_day_before', 'date' => $nextPeriod->format('Y-m-d')];
            }

            // Notify on period day if not logged yet
            if ($daysUntil == 0) {
                // Check if period was already logged today
                $loggedToday = Cycle::where('user_id', $user->id)
                    ->whereDate('period_start_date', $today->format('Y-m-d'))
                    ->exists();

                if (!$loggedToday) {
                    $this->createNotification(
                        $user->id,
                        'user',
                        'Period Today',
                        "Your period is expected today. Don't forget to log it!",
                        'period_today',
                        $today->format('Y-m-d')
                    );
                    $notified[] = ['user_id' => $user->id, 'type' => 'today', 'date' => $today->format('Y-m-d')];
                }
            }
        }

        return $notified;
    }

    /**
     * Create a notification for a user
     *
     * @param int $userId
     * @param string $userRole
     * @param string $title
     * @param string $message
     * @param string $type
     * @param string $referenceDate
     * @return void
     */
    private function createNotification(int $userId, string $userRole, string $title, string $message, string $type, string $referenceDate): void
    {
        $data = [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ];

        if ($userRole === 'user') {
            $data['user_id'] = $userId;
        } elseif ($userRole === 'midwife') {
            $data['midwife_id'] = $userId;
        } elseif ($userRole === 'bhw') {
            $data['bhw_id'] = $userId;
        }

        // Check if notification already exists for this date and type
        $exists = Notification::where(function($q) use ($userId, $userRole) {
                if ($userRole === 'user') {
                    $q->where('user_id', $userId);
                } elseif ($userRole === 'midwife') {
                    $q->where('midwife_id', $userId);
                } elseif ($userRole === 'bhw') {
                    $q->where('bhw_id', $userId);
                }
            })
            ->where('type', $type)
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->exists();

        if (!$exists) {
            Notification::create($data);
        }
    }

    /**
     * Get unread notifications for a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications(int $userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return true;
        }

        return false;
    }
}
