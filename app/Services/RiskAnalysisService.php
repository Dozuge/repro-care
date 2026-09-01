<?php

namespace App\Services;

use App\Models\User;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\Notification;
use App\Models\PreventiveIntervention;
use Carbon\Carbon;

class RiskAnalysisService
{
    /**
     * Evaluate and update risk level for a patient based on multi-attribute clinical parameters.
     * Returns risk level: 'Low', 'Medium', 'High', or 'Critical'
     */
    public function evaluate(int $patientId): string
    {
        $patient = User::with(['checkups', 'healthRecords', 'pregnancies'])->find($patientId);
        if (!$patient) return 'Low';

        $score  = 0;
        $alerts = [];
        $recommendations = [];

        // ── 1. Maternal Age Assessment ─────────────────────────
        $age = $patient->age;
        if ($age !== null) {
            if ($age < 19) {
                $score += 3;
                $alerts[] = "Adolescent/Teenage pregnancy detected (Age: {$age}).";
                $recommendations[] = "👩‍⚕️ Adolescent Health Protocol: Provide supportive nutrition counseling, psychosocial support, and birth preparedness.";
            } elseif ($age >= 35) {
                $score += 2;
                $alerts[] = "Advanced maternal age (Age: {$age} years).";
                $recommendations[] = "🧬 Advanced Age Monitoring: Screen for gestational diabetes, chromosomal anomalies, and hypertensive disorders.";
            }
        }

        // ── 2. Missed & Overdue Checkups ──────────────────────
        $missedCount = $patient->checkups()->missed()->count();
        if ($missedCount >= 3) {
            $score += 4;
            $alerts[] = "Patient has {$missedCount} missed checkups (high care gap).";
            $recommendations[] = "🚨 High Care Gap: Direct BHW home visitation required to reconnect patient with prenatal care.";
        } elseif ($missedCount >= 1) {
            $score += 2;
            $alerts[] = "Patient has {$missedCount} missed checkup(s).";
            $recommendations[] = "📅 Reschedule missed appointment as soon as possible.";
        }

        $overdueCount = $patient->checkups()
            ->where('status', 'Scheduled')
            ->where('scheduled_date', '<', Carbon::now())
            ->count();
        if ($overdueCount > 0) {
            $score += 2;
            $alerts[] = "{$overdueCount} scheduled checkup(s) are overdue.";
        }

        // ── 3. Blood Pressure Trends (Preeclampsia Assessment) ─
        $latestRecord = $patient->healthRecords()->latest()->first();
        if ($latestRecord && ($latestRecord->risk_assessment_mode ?? 'automatic') === 'manual') {
            return $latestRecord->risk_level ?? 'Low';
        }

        if ($latestRecord && $latestRecord->bp) {
            $bp = $this->parseBP($latestRecord->bp);
            if ($bp) {
                if ($bp['systolic'] >= 160 || $bp['diastolic'] >= 110) {
                    $score += 6;
                    $alerts[] = "Severe hypertension / Preeclampsia crisis detected: {$latestRecord->bp} mmHg.";
                    $recommendations[] = "🚨 Critical BP: Immediate hospital referral for emergency antihypertensive therapy and preeclampsia workup.";
                } elseif ($bp['systolic'] >= 140 || $bp['diastolic'] >= 90) {
                    $score += 4;
                    $alerts[] = "High blood pressure (Hypertension Stage 2): {$latestRecord->bp} mmHg.";
                    $recommendations[] = "🩺 Preeclampsia Protocol: Check for proteinuria, monitor BP daily, educate on warning signs (severe headache, visual disturbances, epigastric pain).";
                } elseif ($bp['systolic'] >= 130 || $bp['diastolic'] >= 80) {
                    $score += 2;
                    $alerts[] = "Elevated blood pressure: {$latestRecord->bp} mmHg.";
                    $recommendations[] = "🩺 Weekly BP checks recommended. Emphasize low-sodium diet and adequate rest.";
                }
            }
        }

        // ── 4. Hemoglobin & Anemia Evaluation ─────────────────
        if ($latestRecord && $latestRecord->hemoglobin) {
            $hb = (float)$latestRecord->hemoglobin;
            if ($hb < 7.0) {
                $score += 5;
                $alerts[] = "Severe anemia detected: {$hb} g/dL (Hb < 7.0).";
                $recommendations[] = "🩸 Urgent Anemia Alert: Immediate referral for parenteral iron or blood transfusion preparation before delivery.";
            } elseif ($hb < 11.0) {
                $score += 3;
                $alerts[] = "Anemia detected: {$hb} g/dL (Hb < 11.0).";
                $recommendations[] = "🥗 Iron Supplementation: Increase oral ferrous sulfate + folic acid with Vitamin C. Repeat CBC in 2-4 weeks.";
            }
        }

        // ── 5. Obstetric History & Previous Complications ──────
        $obstetricHistory = strtolower((string)($latestRecord->medical_history ?? $patient->medical_history ?? ''));
        if (!empty($obstetricHistory)) {
            $highRiskComplications = ['preeclampsia', 'eclampsia', 'hemorrhage', 'stillbirth', 'postpartum hemorrhage', 'abruptio', 'placenta previa'];
            $moderateComplications = ['cesarean', 'c-section', 'miscarriage', 'preterm', 'premature'];

            foreach ($highRiskComplications as $comp) {
                if (str_contains($obstetricHistory, $comp)) {
                    $score += 4;
                    $alerts[] = "History of severe complication: " . ucfirst($comp);
                    $recommendations[] = "🏥 High-Risk Obstetric History: Plan facility-based birth at secondary/tertiary hospital with surgical capacity.";
                    break;
                }
            }

            foreach ($moderateComplications as $comp) {
                if (str_contains($obstetricHistory, $comp)) {
                    $score += 2;
                    $alerts[] = "History of prior delivery event: " . ucfirst($comp);
                    $recommendations[] = "📋 Prior Complication Note: Close monitoring of uterine activity and labor onset required.";
                    break;
                }
            }
        }

        // ── 6. Gestational Age & Third Trimester Status ───────
        $activePregnancy = $patient->pregnancies()->active()->first();
        if ($activePregnancy) {
            $aog = (int)($activePregnancy->aog_weeks ?? 0);
            if ($aog >= 36) {
                $daysSinceCheckup = $patient->checkups()
                    ->completed()
                    ->where('scheduled_date', '>=', Carbon::now()->subDays(14))
                    ->count();
                if ($daysSinceCheckup === 0) {
                    $score += 3;
                    $alerts[] = "Third trimester pregnancy ({$aog} weeks) with no checkup in the last 14 days.";
                    $recommendations[] = "🤰 Late Gestation Protocol: Schedule weekly checkup, assess fetal presentation, and verify facility transport readiness.";
                }
            }
        }

        // ── Determine Overall Risk Tier ────────────────────────
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

        // ── Update health record with recommendations ──────────
        if ($latestRecord && !empty($recommendations)) {
            $latestRecord->update(['recommendations' => implode("\n", array_unique($recommendations))]);
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
                case 'Critical': $score += 15; break;
                case 'High':     $score += 10; break;
                case 'Medium':   $score += 5; break;
                default:         $score += 1; break;
            }

            // Teenage pregnancy weight
            if ($patient->age !== null && $patient->age < 19) {
                $score += 5;
            }

            // Missed appointments weight
            $missed = $patient->checkups()->missed()->count();
            $score += min(6, $missed * 2);

            // Days until expected delivery (priority if within 14 days)
            $preg = $patient->pregnancies()->active()->first();
            if ($preg && $preg->expected_delivery_date) {
                $days = now()->diffInDays($preg->expected_delivery_date, false);
                if ($days >= 0 && $days <= 14) {
                    $score += 8;
                } elseif ($days >= 0 && $days <= 30) {
                    $score += 4;
                }
            }

            return ['patient' => $patient, 'risk_level' => $risk, 'priority_score' => $score];
        });

        return collect($scored)->sortByDesc('priority_score')->values();
    }

    /**
     * Trigger preventive interventions and log them
     */
    private function triggerPreventiveInterventions(int $patientId, string $riskLevel, array $alerts, array $recommendations): void
    {
        PreventiveIntervention::create([
            'patient_id' => $patientId,
            'risk_level' => $riskLevel,
            'trigger_reason' => implode('; ', $alerts),
            'recommendations' => implode("\n", array_unique($recommendations)),
            'intervention_type' => $this->determineInterventionType($alerts),
            'status' => 'triggered',
            'triggered_at' => now(),
        ]);

        // Create follow-up tasks based on risk level
        if ($riskLevel === 'Critical') {
            $this->createFollowUpCheckup($patientId, 1, 'URGENT CRITICAL: Immediate clinical follow-up required within 24 hours');
        } elseif ($riskLevel === 'High') {
            $this->createFollowUpCheckup($patientId, 2, 'Urgent: High risk patient follow-up required within 48 hours');
        } elseif ($riskLevel === 'Medium') {
            $this->createFollowUpCheckup($patientId, 7, 'Medium risk patient follow-up recommended within 1 week');
        }
    }

    /**
     * Determine intervention type based on alerts
     */
    private function determineInterventionType(array $alerts): string
    {
        $text = strtolower(implode(' ', $alerts));
        if (str_contains($text, 'hypertension') || str_contains($text, 'blood pressure') || str_contains($text, 'preeclampsia')) {
            return 'hypertension_management';
        }
        if (str_contains($text, 'anemia') || str_contains($text, 'hemoglobin')) {
            return 'anemia_prevention';
        }
        if (str_contains($text, 'teenage') || str_contains($text, 'adolescent')) {
            return 'adolescent_care';
        }
        if (str_contains($text, 'checkup') || str_contains($text, 'gap')) {
            return 'care_continuity';
        }
        if (str_contains($text, 'trimester') || str_contains($text, 'gestation')) {
            return 'pregnancy_monitoring';
        }
        return 'general_health';
    }

    /**
     * Create automatic follow-up checkup if one is not already scheduled
     */
    private function createFollowUpCheckup(int $patientId, int $daysFromNow, string $notes): void
    {
        $existingCheckup = Checkup::where('user_id', $patientId)
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays($daysFromNow + 1))
            ->where('status', 'Scheduled')
            ->exists();

        if (!$existingCheckup) {
            Checkup::create([
                'user_id' => $patientId,
                'scheduled_date' => now()->addDays($daysFromNow),
                'status' => 'Scheduled',
                'purpose' => 'Follow-up: Risk-based clinical care',
                'notes' => $notes,
                'scheduled_by_midwife_id' => 1,
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
