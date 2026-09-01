<?php

namespace App\Services;

use App\Models\User;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\Notification;
use Carbon\Carbon;

class RiskAnalysisService
{
    /**
     * Evaluate and update risk level for a patient.
     * Returns risk level: 'Low', 'Medium', 'High', or 'Critical'
     */
    public function evaluate(int $patientId): string
    {
        $patient = User::with(['checkups', 'healthRecords'])->find($patientId);
        if (!$patient) return 'Low';

        $score  = 0;
        $alerts = [];

        // ── 1. Missed Checkups ─────────────────────────────────
        $missedCount = $patient->checkups()->missed()->count();
        if ($missedCount >= 3) {
            $score += 3;
            $alerts[] = "Patient has {$missedCount} missed checkups.";
        } elseif ($missedCount >= 1) {
            $score += 1;
            $alerts[] = "Patient has {$missedCount} missed checkup(s).";
        }

        // ── 2. Overdue Scheduled Checkups ─────────────────────
        $overdueCount = $patient->checkups()
            ->where('status', 'Scheduled')
            ->where('scheduled_date', '<', Carbon::now())
            ->count();
        if ($overdueCount > 0) {
            $score += 2;
            $alerts[] = "{$overdueCount} scheduled checkup(s) are overdue.";
        }

        // ── 3. High Blood Pressure (latest record) ─────────────
        $latestRecord = $patient->healthRecords()->latest()->first();
        if ($latestRecord && ($latestRecord->risk_assessment_mode ?? 'automatic') === 'manual') {
            return $latestRecord->risk_level ?? 'Low';
        }
        if ($latestRecord && $latestRecord->bp) {
            $bp = $this->parseBP($latestRecord->bp);
            if ($bp && ($bp['systolic'] >= 140 || $bp['diastolic'] >= 90)) {
                $score += 3;
                $alerts[] = "High blood pressure detected: {$latestRecord->bp}";
            } elseif ($bp && ($bp['systolic'] >= 130 || $bp['diastolic'] >= 85)) {
                $score += 1;
                $alerts[] = "Elevated blood pressure: {$latestRecord->bp}";
            }
        }

        // ── 4. Low Hemoglobin ──────────────────────────────────
        if ($latestRecord && $latestRecord->hemoglobin) {
            if ((float)$latestRecord->hemoglobin < 11.0) {
                $score += 2;
                $alerts[] = "Low hemoglobin: {$latestRecord->hemoglobin} g/dL";
            }
        }

        // ── 5. Late Pregnancy without recent checkup ───────────
        $activePregnancy = $patient->pregnancies()->active()->first();
        if ($activePregnancy && $activePregnancy->aog_weeks >= 36) {
            $daysSinceCheckup = $patient->checkups()
                ->completed()
                ->where('scheduled_date', '>=', Carbon::now()->subDays(14))
                ->count();
            if ($daysSinceCheckup === 0) {
                $score += 3;
                $alerts[] = "Patient is {$activePregnancy->aog_weeks} weeks pregnant with no recent checkup.";
            }
        }

        // ── Determine Risk Level ──────────────────────────────
        if ($score >= 8) {
            $riskLevel = 'Critical';
        } elseif ($score >= 5) {
            $riskLevel = 'High';
        } elseif ($score >= 2) {
            $riskLevel = 'Medium';
        } else {
            $riskLevel = 'Low';
        }

        // ── Update latest health record risk level ────────────
        if ($latestRecord && $latestRecord->risk_level !== $riskLevel) {
            $latestRecord->update(['risk_level' => $riskLevel]);
        }

        // ── Generate Recommendations ───────────────────────────
        $recommendations = $this->generateRecommendations($alerts, $latestRecord);

        // ── Update health record with recommendations ──────────
        if ($latestRecord && !empty($recommendations)) {
            $latestRecord->update(['recommendations' => implode("\n", $recommendations)]);
        }

        // ── Trigger Preventive Interventions ────────────────────
        if ($riskLevel !== 'Low') {
            $this->triggerPreventiveInterventions($patientId, $riskLevel, $alerts, $recommendations);
        }

        // ── Trigger Smart Notifications if Medium/High ─────────
        if ($riskLevel !== 'Low' && !empty($alerts)) {
            $notificationService = new SmartNotificationService();
            $notificationService->notifyHighRisk($patient, implode(' ', $alerts), $riskLevel);
        }

        return $riskLevel;
    }

    /**
     * Prioritize patients across the system based on risk and other factors
     * Returns an ordered collection: highest priority first
     */
    public function prioritize(array $patientIds)
    {
        $patients = User::whereIn('id', $patientIds)->with(['pregnancies', 'checkups', 'healthRecords'])->get();

        $scored = $patients->map(function ($patient) {
            $risk = $this->evaluate($patient->id);
            $score = 0;

            switch ($risk) {
                case 'Critical': $score += 10; break;
                case 'High':     $score += 6; break;
                case 'Medium':   $score += 3; break;
                default:         $score += 0; break;
            }

            // Missed appointments weight
            $missed = $patient->checkups()->missed()->count();
            $score += min(5, $missed * 2);

            // Days until expected delivery (priority if within 14 days)
            $preg = $patient->pregnancies()->active()->first();
            if ($preg && $preg->expected_delivery_date) {
                $days = now()->diffInDays($preg->expected_delivery_date, false);
                if ($days <= 14) {
                    $score += 5;
                } elseif ($days <= 30) {
                    $score += 2;
                }
            }

            return ['patient' => $patient, 'priority_score' => $score];
        });

        return collect($scored)->sortByDesc('priority_score')->values();
    }

    /**
     * Generate recommendations based on detected risk factors
     */
    private function generateRecommendations(array $alerts, ?HealthRecord $record): array
    {
        $recommendations = [];

        foreach ($alerts as $alert) {
            if (str_contains($alert, 'missed checkup')) {
                $recommendations[] = "📅 Schedule immediate follow-up checkup to assess health status.";
            }
            if (str_contains($alert, 'overdue')) {
                $recommendations[] = "⏰ Reschedule missed appointment as soon as possible.";
            }
            if (str_contains($alert, 'High blood pressure') || str_contains($alert, 'Elevated blood pressure')) {
                $recommendations[] = "🩺 Monitor BP daily. Reduce salt intake. Consider referral to physician for hypertension management.";
                $recommendations[] = "💊 May need antihypertensive medication if BP remains elevated.";
            }
            if (str_contains($alert, 'Low hemoglobin')) {
                $recommendations[] = "🥗 Increase iron-rich foods (red meat, leafy greens, beans). Take iron supplements if prescribed.";
                $recommendations[] = "🍊 Include vitamin C with meals to enhance iron absorption.";
            }
            if (str_contains($alert, 'weeks pregnant')) {
                $recommendations[] = "🤰 Weekly checkups required in late pregnancy. Monitor for signs of labor.";
            }
        }

        // General recommendations for Medium/High risk
        if (count($recommendations) > 0) {
            $recommendations[] = "📞 Contact health worker if symptoms worsen or new concerns arise.";
        }

        return $recommendations;
    }

    /**
     * Trigger preventive interventions and log them
     */
    private function triggerPreventiveInterventions(int $patientId, string $riskLevel, array $alerts, array $recommendations): void
    {
        // Log the preventive intervention
        \App\Models\PreventiveIntervention::create([
            'patient_id' => $patientId,
            'risk_level' => $riskLevel,
            'trigger_reason' => implode('; ', $alerts),
            'recommendations' => implode("\n", $recommendations),
            'intervention_type' => $this->determineInterventionType($alerts),
            'status' => 'triggered',
            'triggered_at' => now(),
        ]);

        // Create follow-up tasks based on risk level
        if ($riskLevel === 'High') {
            // High risk: Schedule immediate checkup within 24-48 hours
            $this->createFollowUpCheckup($patientId, 1, 'Urgent: High risk patient follow-up required');
        } elseif ($riskLevel === 'Medium') {
            // Medium risk: Schedule follow-up within 1 week
            $this->createFollowUpCheckup($patientId, 7, 'Medium risk patient follow-up recommended');
        }
    }

    /**
     * Determine intervention type based on alerts
     */
    private function determineInterventionType(array $alerts): string
    {
        if (str_contains(implode(' ', $alerts), 'blood pressure')) {
            return 'hypertension_management';
        }
        if (str_contains(implode(' ', $alerts), 'hemoglobin')) {
            return 'anemia_prevention';
        }
        if (str_contains(implode(' ', $alerts), 'checkup')) {
            return 'care_continuity';
        }
        if (str_contains(implode(' ', $alerts), 'pregnant')) {
            return 'pregnancy_monitoring';
        }
        return 'general_health';
    }

    /**
     * Create automatic follow-up checkup
     */
    private function createFollowUpCheckup(int $patientId, int $daysFromNow, string $notes): void
    {
        // Check if a checkup already exists for this date range
        $existingCheckup = \App\Models\Checkup::where('user_id', $patientId)
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays($daysFromNow + 1))
            ->where('status', 'Scheduled')
            ->exists();

        if (!$existingCheckup) {
            \App\Models\Checkup::create([
                'user_id' => $patientId,
                'scheduled_date' => now()->addDays($daysFromNow),
                'status' => 'Scheduled',
                'purpose' => 'Follow-up: Risk-based preventive care',
                'notes' => $notes,
                'scheduled_by_midwife_id' => 1, // System/Admin ID
            ]);
        }
    }

    /**
     * Parse BP string "120/80" → ['systolic' => 120, 'diastolic' => 80]
     */
    private function parseBP(string $bp): ?array
    {
        $parts = explode('/', $bp);
        if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
            return [
                'systolic'  => (int)trim($parts[0]),
                'diastolic' => (int)trim($parts[1]),
            ];
        }
        return null;
    }
}
