<?php

namespace App\Services;

class MaternalRiskService
{
    public function assess(array $data): array
    {
        $reasons = [];
        $score = 0;
        $age = isset($data['age']) && is_numeric($data['age']) ? (int) $data['age'] : null;
        $bmi = $this->calculateBmi($data['weight'] ?? null, $data['height'] ?? null);
        $bp = $this->parseBloodPressure($data['blood_pressure'] ?? $data['bp'] ?? null);
        $history = strtolower(trim((string) ($data['obstetric_history'] ?? '')));
        $healthConditions = collect((array) ($data['health_conditions'] ?? []))
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->values()
            ->all();
        $healthConditionOther = trim((string) ($data['health_condition_other'] ?? ''));

        if ($age !== null && ($age < 18 || $age >= 35)) {
            $score += 2;
            $reasons[] = 'Maternal age is outside the usual low-risk range.';
        }

        if ($bp !== null) {
            if ($bp['systolic'] >= 140 || $bp['diastolic'] >= 90) {
                $score += 3;
                $reasons[] = 'Blood pressure is in the high-risk range.';
            } elseif ($bp['systolic'] >= 130 || $bp['diastolic'] >= 80) {
                $score += 1;
                $reasons[] = 'Blood pressure is elevated.';
            }
        }

        if ($bmi !== null) {
            if ($bmi < 18.5 || $bmi >= 30) {
                $score += 2;
                $reasons[] = 'BMI is outside the usual low-risk range.';
            } elseif ($bmi >= 25) {
                $score += 1;
                $reasons[] = 'BMI is above the recommended range.';
            }
        }

        if (($data['smoking_status'] ?? '') === 'current') {
            $score += 2;
            $reasons[] = 'Current smoking raises pregnancy risk.';
        }

        if (($data['alcohol_status'] ?? $data['alcohol_use'] ?? '') === 'current') {
            $score += 1;
            $reasons[] = 'Alcohol use requires closer monitoring.';
        }

        if (($data['drug_use_status'] ?? $data['drug_use'] ?? '') === 'current') {
            $score += 3;
            $reasons[] = 'Drug use places the pregnancy in a high-risk category.';
        }

        if ($healthConditions !== [] || $healthConditionOther !== '') {
            $score += 2;
            $reasons[] = 'Existing maternal health conditions require closer monitoring.';
        }

        if (array_intersect($healthConditions, ['pre_existing_hypertension', 'renal_disease', 'blood_disorders'])) {
            $score += 2;
            $reasons[] = 'A listed health condition increases maternal risk.';
        }

        if ($history !== '') {
            foreach (['miscarriage', 'stillbirth', 'preeclampsia', 'eclampsia', 'hemorrhage', 'preterm', 'cesarean', 'c-section', 'twins', 'multiple'] as $keyword) {
                if (str_contains($history, $keyword)) {
                    $score += 2;
                    $reasons[] = 'Obstetric history includes prior complications.';
                    break;
                }
            }
        }

        $riskLevel = $score >= 5 ? 'High' : ($score >= 2 ? 'Medium' : 'Low');

        return [
            'risk_level' => $riskLevel,
            'is_high_risk' => $riskLevel === 'High',
            'bmi' => $bmi,
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    public function calculateBmi($weight, $heightCm): ?float
    {
        if (!is_numeric($weight) || !is_numeric($heightCm) || (float) $heightCm <= 0) {
            return null;
        }

        $heightMeters = ((float) $heightCm) / 100;
        if ($heightMeters <= 0) {
            return null;
        }

        return round(((float) $weight) / ($heightMeters * $heightMeters), 2);
    }

    public function parseBloodPressure(?string $bp): ?array
    {
        if (!$bp || !str_contains($bp, '/')) {
            return null;
        }

        [$systolic, $diastolic] = array_map('trim', explode('/', $bp, 2));
        if (!is_numeric($systolic) || !is_numeric($diastolic)) {
            return null;
        }

        return [
            'systolic' => (int) $systolic,
            'diastolic' => (int) $diastolic,
        ];
    }
}
