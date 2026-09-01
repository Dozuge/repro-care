<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SendScheduledMessages implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $scheduledMessages = Message::where('is_sent', false)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($scheduledMessages as $message) {
            $message->update(['is_sent' => true]);

            $receiverRole = $message->receiver?->role;
            $routePrefix = match ($receiverRole) {
                'midwife' => 'midwife',
                'bhw' => 'bhw',
                'bhw_president' => 'bhw-president',
                default => 'user',
            };

            Notification::createNotification(
                $message->receiver_id,
                'New Message from ' . optional($message->sender)->name . ': ' . Str::limit($message->body, 60),
                'New Message',
                'info',
                '/' . $routePrefix . '/messages/' . ($message->reply_to_id ?: $message->id),
                $receiverRole
            );
        }
    }
}
