<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Checkup;
use App\Models\User;
use App\Services\RiskAnalysisService;
use App\Services\PeriodNotificationService;
use App\Services\SmsService;
use App\Services\SmartNotificationService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// AUTOMATED ANALYSIS & ALERTS SCHEDULER
// ============================================================

// 1. Mark overdue checkups as "Missed" and notify - runs daily at 8:00 AM
Schedule::call(function () {
    Checkup::markOverdueCheckups();
})->dailyAt('08:00')
  ->description('Mark overdue scheduled checkups as Missed and send notifications');

// 2. Re-evaluate risk for patients with missed checkups - runs daily at 8:30 AM
Schedule::call(function () {
    $missedCheckups = Checkup::missed()
        ->whereDate('updated_at', today())
        ->distinct('patient_id')
        ->pluck('patient_id');

    foreach ($missedCheckups as $patientId) {
        $riskService = new RiskAnalysisService();
        $riskService->evaluate($patientId);
    }
})->dailyAt('08:30')
  ->description('Re-evaluate risk for patients with newly missed checkups');

// 3. Period tracking notifications - runs daily at 9:00 AM
Schedule::call(function () {
    $service = new PeriodNotificationService();
    $service->checkAndNotify();
})->dailyAt('09:00')
  ->description('Send period reminders (3 days before, 1 day before, day of)');

// 4. Risk re-evaluation for high-risk patients - runs weekly on Sundays at 10:00 AM
Schedule::call(function () {
    $highRiskPatients = \App\Models\HealthRecord::highRisk()
        ->distinct('patient_id')
        ->pluck('patient_id');

    foreach ($highRiskPatients as $patientId) {
        $riskService = new RiskAnalysisService();
        $riskService->evaluate($patientId);
    }
})->weeklyOn(0, '10:00')
  ->description('Weekly re-evaluation of all high-risk patients');

// ============================================================
// SMS ALERTS SCHEDULER
// ============================================================

// 5. Appointment Reminder SMS — runs daily at 7:00 AM
//    Finds all checkups scheduled for TOMORROW and sends SMS reminders.
Schedule::call(function () {
    $smsService = new SmsService();
    $notifyService = new SmartNotificationService();

    $tomorrow = Carbon::tomorrow()->toDateString();

    $checkups = Checkup::where('status', 'Scheduled')
        ->whereDate('scheduled_date', $tomorrow)
        ->with('woman')
        ->get();

    foreach ($checkups as $checkup) {
        if ($checkup->woman && $checkup->woman->hasSmsEnabled()) {
            // In-app notification + SMS (SmartNotificationService handles both)
            $notifyService->notifyUpcomingCheckup($checkup);
        }
    }
})->dailyAt('07:00')
  ->description('Send appointment reminder SMS to patients with checkups tomorrow');

// 6. Weekly AI Insight Cache Flush — runs every Sunday at 02:00 AM
//    Clears the cached Gemini insights so fresh data is generated on Monday morning.
Schedule::call(function () {
    // Clear all AI insight caches (pattern: ai_insights_*)
    Cache::flush();
})->weeklyOn(0, '02:00')
  ->description('Flush AI insights cache for fresh weekly analytics');

