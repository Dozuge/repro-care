<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SmsService
{
    // ── Bilingual Message Templates ────────────────────────────────────────────

    private array $templates = [
        'appointment_reminder' => [
            'en' => "📅 REPROCARE REMINDER: Hi {name}, you have a checkup scheduled on {date} at {time}. Please do not miss it. Contact your health worker for details.",
            'tl' => "📅 REPROCARE PAALALA: Kumusta {name}, mayroon kang checkup sa {date} ng {time}. Huwag kalimutang dumalo. Makipag-ugnayan sa iyong health worker para sa detalye.",
        ],
        'high_risk_alert' => [
            'en' => "🚨 REPROCARE HEALTH ALERT: Hi {name}, our system has detected a health concern: {reason}. Please contact your health worker immediately or visit the nearest health facility.",
            'tl' => "🚨 REPROCARE BABALA SA KALUSUGAN: Kumusta {name}, natukoy ng aming sistema ang isang alalahanin sa kalusugan: {reason}. Makipag-ugnayan kaagad sa iyong health worker o pumunta sa pinakamalapit na health facility.",
        ],
        'missed_checkup' => [
            'en' => "⛔ REPROCARE NOTICE: Hi {name}, you missed your scheduled checkup on {date}. Please reschedule as soon as possible. Your health matters!",
            'tl' => "⛔ REPROCARE ABISO: Kumusta {name}, napalampas mo ang iyong nakatalagang checkup noong {date}. Mangyaring mag-iskedyul muli sa lalong madaling panahon. Mahalaga ang iyong kalusugan!",
        ],
        'custom' => [
            'en' => "{message}",
            'tl' => "{message_tl}",
        ],
        'broadcast' => [
            'en' => "📢 REPROCARE ANNOUNCEMENT: {message}",
            'tl' => "📢 REPROCARE ANUNSYO: {message}",
        ],
    ];

    // ── Public Interface ────────────────────────────────────────────────────────

    /**
     * Send a templated SMS to a registered User.
     * Checks opt-out and contact number before sending.
     */
    public function send(User $user, string $type, array $params = []): bool
    {
        if (!$user->hasSmsEnabled()) {
            Log::info("SmsService: SMS skipped for user {$user->id} — opt-out or no contact number.");
            return false;
        }

        $message = $this->buildBilingualMessage($type, $params, $user->first_name);
        return $this->dispatch($user->contact_number, $message, $type, $user->id);
    }

    /**
     * Send a custom bilingual SMS to a registered User.
     * $en = English message, $tl = Tagalog message
     */
    public function sendCustom(User $user, string $en, string $tl = ''): bool
    {
        if (!$user->hasSmsEnabled()) {
            return false;
        }

        $message = $en;
        if (!empty($tl)) {
            $message .= "\n\n" . $tl;
        }

        return $this->dispatch($user->contact_number, $message, 'custom', $user->id);
    }

    /**
     * Send an SMS directly to a phone number (no user association required).
     */
    public function sendToPhone(string $phone, string $message, string $type = 'custom'): bool
    {
        return $this->dispatch($phone, $message, $type, null);
    }

    /**
     * Send a broadcast SMS to all patients in a given purok or barangay.
     * Rate-limited to 500 per call.
     */
    public function broadcast(string $enMessage, string $tlMessage = '', ?int $purokId = null, ?string $barangay = null): int
    {
        $query = User::where('role', 'user')
            ->where('status', 'approved')
            ->where('sms_opt_out', false)
            ->whereNotNull('contact_number')
            ->where('contact_number', '!=', '');

        if ($purokId) {
            $query->where('purok_id', $purokId);
        }

        if ($barangay) {
            $query->where('barangay', $barangay);
        }

        $users = $query->limit(500)->get();
        $sent = 0;

        foreach ($users as $user) {
            $message = $enMessage;
            if (!empty($tlMessage)) {
                $message .= "\n\n" . $tlMessage;
            }

            if ($this->dispatch($user->contact_number, $message, 'broadcast', $user->id)) {
                $sent++;
            }
        }

        return $sent;
    }

    // ── Internal ────────────────────────────────────────────────────────────────

    /**
     * Build a bilingual message string from a template type + params.
     */
    private function buildBilingualMessage(string $type, array $params, string $firstName = ''): string
    {
        $template = $this->templates[$type] ?? $this->templates['custom'];

        $defaults = [
            'name'       => $firstName,
            'date'       => $params['date'] ?? 'N/A',
            'time'       => $params['time'] ?? '',
            'reason'     => $params['reason'] ?? 'a health concern was detected',
            'message'    => $params['message'] ?? '',
            'message_tl' => $params['message_tl'] ?? $params['message'] ?? '',
        ];

        $enMessage = $this->interpolate($template['en'], $defaults);
        $tlMessage = $this->interpolate($template['tl'], $defaults);

        // If both are the same (custom with no TL), just return one
        if ($enMessage === $tlMessage || empty($tlMessage)) {
            return $enMessage;
        }

        return $enMessage . "\n\n" . $tlMessage;
    }

    /**
     * Replace {placeholders} in a template string.
     */
    private function interpolate(string $template, array $params): string
    {
        foreach ($params as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

    /**
     * Dispatch the actual SMS via FMCSMS (fortmed.org) API using cURL and log the result.
     *
     * API Endpoint: POST https://fortmed.org/web/FMCSMS/api/messages.php
     * Auth Header:  X-API-Key: <api_key>
     * JSON Body:    { SenderName, ToNumber, MessageBody, FromNumber }
     */
    private function dispatch(string $phone, string $message, string $type, ?int $userId): bool
    {
        $phone = $this->normalizePhilippinesPhone($phone);

        // Build log record first as pending
        $log = SmsLog::create([
            'user_id'      => $userId,
            'phone_number' => $phone,
            'message'      => $message,
            'type'         => $type,
            'status'       => 'pending',
        ]);

        // Read FMCSMS config
        $apiKey     = config('services.fmcsms.api_key');
        $apiUrl     = config('services.fmcsms.api_url');
        $senderName = config('services.fmcsms.sender_name', 'REPROCARE');
        $fromNumber = config('services.fmcsms.from_number');
        $mock       = config('services.fmcsms.mock');

        // ── MOCK MODE ──────────────────────────────────────────────────
        if ($mock) {
            $log->update([
                'status'       => 'sent',
                'provider_sid' => 'MOCK_' . \Illuminate\Support\Str::random(10),
                'sent_at'      => now(),
            ]);
            Log::info("SmsService: [MOCK] SMS sent to {$phone}. Type: {$type}.");
            return true;
        }

        // ── Validate configuration ─────────────────────────────────────
        if (empty($apiKey) || empty($apiUrl)) {
            Log::warning("SmsService: FMCSMS not configured. SMS not sent to {$phone}. Message: {$message}");
            $log->update(['status' => 'failed', 'error_message' => 'FMCSMS API key or URL not configured.']);
            return false;
        }

        // ── Send via FMCSMS cURL ───────────────────────────────────────
        try {
            $payload = json_encode([
                'SenderName'  => $senderName,
                'ToNumber'    => $phone,
                'MessageBody' => $message,
                'FromNumber'  => $fromNumber,
            ]);

            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'X-API-Key: ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $responseBody = curl_exec($ch);
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError    = curl_error($ch);
            curl_close($ch);

            // Handle cURL-level errors (network failure, timeout, etc.)
            if ($responseBody === false || !empty($curlError)) {
                $errorMsg = 'cURL error: ' . ($curlError ?: 'No response from FMCSMS API');
                Log::error("SmsService: {$errorMsg}");
                $log->update(['status' => 'failed', 'error_message' => $errorMsg]);
                return false;
            }

            $response = json_decode($responseBody, true);

            // ── Evaluate the API response ──────────────────────────────
            // Success: HTTP 200/201 and response indicates success
            if ($httpCode >= 200 && $httpCode < 300 && $this->isSuccessResponse($response)) {
                $providerSid = $response['message_id']
                    ?? $response['id']
                    ?? $response['sid']
                    ?? ('FMCSMS_' . \Illuminate\Support\Str::random(10));

                $log->update([
                    'status'       => 'sent',
                    'provider_sid' => $providerSid,
                    'sent_at'      => now(),
                ]);

                Log::info("SmsService: SMS sent to {$phone}. Type: {$type}. Provider SID: {$providerSid}");
                return true;
            }

            // ── API returned an error ──────────────────────────────────
            $errorMsg = $response['error']
                ?? $response['message']
                ?? $response['error_message']
                ?? "HTTP {$httpCode}: " . substr($responseBody, 0, 500);

            $log->update([
                'status'        => 'failed',
                'error_message' => $errorMsg,
            ]);

            Log::error("SmsService: FMCSMS API error for {$phone}. HTTP {$httpCode}. Response: {$responseBody}");
            return false;

        } catch (\Throwable $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("SmsService: Exception sending SMS to {$phone}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Determine if the FMCSMS API response indicates success.
     */
    private function isSuccessResponse(?array $response): bool
    {
        if (!$response) {
            return false;
        }

        // Check common success indicators in the response
        if (isset($response['success']) && $response['success']) {
            return true;
        }
        if (isset($response['status']) && in_array(strtolower($response['status']), ['sent', 'queued', 'success', 'ok', 'accepted'])) {
            return true;
        }
        if (isset($response['message_id']) || isset($response['id'])) {
            return true;
        }

        return false;
    }

    /**
     * Normalize a Philippine phone number to E.164 format (+63).
     * e.g. 09171234567 → +639171234567
     *      639171234567 → +639171234567
     */
    public function normalizePhilippinesPhone(string $phone): string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '+63' . substr($digits, 1);
        }

        if (str_starts_with($digits, '9') && strlen($digits) === 10) {
            return '+63' . $digits;
        }

        // Return as-is if already formatted or unrecognized
        return str_starts_with($phone, '+') ? $phone : '+' . $digits;
    }
}
