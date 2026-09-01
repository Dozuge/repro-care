<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIInsightService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.api_key', '');
        $this->model    = config('services.gemini.model', 'gemini-1.5-flash');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    // ── Public Interface ──────────────────────────────────────────────────────

    /**
     * Generate structured strategic interventions based on dynamic threshold evaluations.
     */
    public function generateStrategicInterventions(array $data): array
    {
        $interventions = [];

        $ancRate          = $data['ancCoverageRate'] ?? 0;
        $facilityRate     = $data['facilityBirthRate'] ?? 0;
        $highRiskCount    = $data['highRiskCount'] ?? 0;
        $totalPregnant    = $data['totalPregnant'] ?? 0;
        $totalDeaths      = $data['totalDeaths'] ?? 0;
        $totalNearMiss    = $data['totalNearMiss'] ?? 0;
        $teenPregnancies  = $data['teenPregnancies'] ?? 0;
        $teenHotspots     = $data['teenHotspots'] ?? [];
        $highRiskHotspots = $data['highRiskHotspots'] ?? [];

        // ── 1. Adolescent Pregnancy Threshold ──────────────────────
        if ($teenPregnancies > 0 || !empty($teenHotspots)) {
            $hotspotText = !empty($teenHotspots) ? "notably in " . implode(', ', array_slice($teenHotspots, 0, 3)) : "city-wide";
            $interventions[] = [
                'id'          => 'teen_pregnancy_surge',
                'category'    => 'Adolescent Reproductive Health',
                'severity'    => $teenPregnancies >= 5 ? 'critical' : 'warning',
                'title'       => "Teenage Pregnancy Cluster Detected ({$teenPregnancies} active cases)",
                'trigger'     => "Adolescent pregnancy records elevated ({$hotspotText})",
                'actions'     => [
                    "Deploy targeted adolescent reproductive health drives in {$hotspotText}.",
                    "Increase RHU youth-friendly contraceptive & family planning counseling hours.",
                    "Partner with Sangguniang Kabataan (SK) and local high schools for peer outreach & sex education.",
                ],
                'badge'       => 'Adolescent Care Priority',
            ];
        }

        // ── 2. Maternal Mortality & Severe Morbidity Threshold ─────
        if ($totalDeaths > 0 || $totalNearMiss > 0) {
            $actions = [];
            if ($totalDeaths > 0) {
                $actions[] = "Conduct multidisciplinary Maternal Death Review & Clinical Audit for {$totalDeaths} recorded case(s).";
            }
            if ($totalNearMiss > 0) {
                $actions[] = "Audit Magnesium Sulfate and emergency blood bank availability across all delivery points.";
            }
            $actions[] = "Review and tighten RHU-to-Hospital emergency obstetric referral protocols.";

            $interventions[] = [
                'id'          => 'maternal_mortality_audit',
                'category'    => 'Emergency Obstetric Care',
                'severity'    => 'critical',
                'title'       => "Emergency Obstetric Audit Alert ({$totalDeaths} Deaths, {$totalNearMiss} Near-Misses)",
                'trigger'     => "Severe maternal morbidity or mortality incident logged",
                'actions'     => $actions,
                'badge'       => 'Urgent Clinical Audit',
            ];
        }

        // ── 3. High-Risk Pregnancy Cluster ─────────────────────────
        if ($highRiskCount > 0 && $totalPregnant > 0) {
            $pct = round(($highRiskCount / $totalPregnant) * 100, 1);
            $hotspotText = !empty($highRiskHotspots) ? "concentrated in " . implode(', ', array_slice($highRiskHotspots, 0, 3)) : "";

            if ($pct >= 20 || $highRiskCount >= 5) {
                $interventions[] = [
                    'id'          => 'high_risk_triage',
                    'category'    => 'High-Risk Maternal Surveillance',
                    'severity'    => $pct >= 35 ? 'critical' : 'warning',
                    'title'       => "High-Risk Pregnancy Surge ({$highRiskCount} cases / {$pct}% of active cases)",
                    'trigger'     => "High-risk pregnancy ratio exceeds normal threshold {$hotspotText}",
                    'actions'     => [
                        "Mandate bi-weekly midwife home visits and clinical surveillance for all flagged high-risk mothers.",
                        "Provide subsidized transport vouchers for high-risk patients to access tertiary ultrasound and OB consultations.",
                        "Distribute home BP monitoring kits to barangay health stations with high preeclampsia incidence.",
                    ],
                    'badge'       => 'High-Risk Triage',
                ];
            }
        }

        // ── 4. Antenatal Care (ANC) Benchmark ──────────────────────
        if ($ancRate < 80) {
            $interventions[] = [
                'id'          => 'anc_compliance_gap',
                'category'    => 'Antenatal Care Compliance',
                'severity'    => $ancRate < 60 ? 'critical' : 'warning',
                'title'       => "ANC 4+ Visit Compliance Deficit ({$ancRate}% vs 80% DOH Target)",
                'trigger'     => "Antenatal care 4-visit completion rate is below the 80% DOH standard",
                'actions'     => [
                    "Mobilize BHWs for door-to-door first-trimester tracking in lagging puroks.",
                    "Broadcast automated prenatal checkup SMS reminders to registered pregnant mothers.",
                    "Host monthly Saturday 'Buntis Day' prenatal clinics at RHU facilities.",
                ],
                'badge'       => 'ANC Target Deficit',
            ];
        }

        // ── 5. Facility-Based Delivery Rate ────────────────────────
        if ($facilityRate < 90) {
            $interventions[] = [
                'id'          => 'facility_delivery_drive',
                'category'    => 'Facility-Based Delivery Promotion',
                'severity'    => 'warning',
                'title'       => "Facility-Based Delivery Rate at {$facilityRate}% (Target: 95%+)",
                'trigger'     => "Home delivery risks detected in recent delivery statistics",
                'actions'     => [
                    "Enforce PhilHealth Maternity Care Package (MCP) registration for all 3rd trimester mothers.",
                    "Strengthen RHU birth center round-the-clock staffing and emergency transport readiness.",
                ],
                'badge'       => 'Facility Delivery Promotion',
            ];
        }

        // Fallback default if all metrics are optimal
        if (empty($interventions)) {
            $interventions[] = [
                'id'          => 'routine_maintenance',
                'category'    => 'System Surveillance Optimal',
                'severity'    => 'info',
                'title'       => "Maternal Health Indicators Operating Within Normal Parameters",
                'trigger'     => "All city-wide indicators meet or exceed DOH targets",
                'actions'     => [
                    "Maintain active BHW surveillance and regular prenatal tracking.",
                    "Continue weekly educational video broadcasts for pregnant patients.",
                ],
                'badge'       => 'Optimal Surveillance',
            ];
        }

        return $interventions;
    }

    /**
     * Generate smart health insights from aggregated analytics data.
     */
    public function generateInsights(array $analyticsData): string
    {
        return $this->fallbackInsights($analyticsData);
    }

    /**
     * Answer a free-form question from a CHO administrator using current analytics context.
     */
    public function chat(string $question, array $analyticsContext = []): string
    {
        if (empty($this->apiKey)) {
            return "⚠️ AI chat is not configured. Please set your GEMINI_API_KEY in the environment settings to enable this feature.";
        }

        $prompt = $this->buildChatPrompt($question, $analyticsContext);
        return $this->callGemini($prompt);
    }

    private function buildChatPrompt(string $question, array $context): string
    {
        $contextJson = !empty($context) ? json_encode($context, JSON_PRETTY_PRINT) : 'Not available';

        return <<<PROMPT
You are a maternal and child health advisor for a city health office in the Philippines.
You are assisting a City Health Officer (CHO) who has a question about their health data.

Current health statistics context:
{$contextJson}

The CHO asks: "{$question}"

Provide a helpful, specific, and actionable response in 2-4 sentences.
PROMPT;
    }

    private function callGemini(string $prompt): string
    {
        $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 1024,
                ],
            ]);

            if ($response->failed()) {
                Log::error('AIInsightService: Gemini API error: ' . $response->body());
                return "⚠️ Unable to generate AI insights at this time.";
            }

            $body = $response->json();
            return trim($body['candidates'][0]['content']['parts'][0]['text'] ?? "No response generated.");

        } catch (\Throwable $e) {
            Log::error('AIInsightService: Exception calling Gemini: ' . $e->getMessage());
            return "⚠️ AI insights encountered an error: " . $e->getMessage();
        }
    }

    private function fallbackInsights(array $data): string
    {
        $insights = [];
        $n = 1;

        $ancRate          = $data['ancCoverageRate']          ?? 0;
        $facilityRate     = $data['facilityBirthRate']        ?? 0;
        $highRisk         = $data['highRiskCount']            ?? 0;
        $totalPregnant    = $data['totalPregnant']            ?? 0;
        $totalDeaths      = $data['totalDeaths']              ?? 0;
        $totalNearMiss    = $data['totalNearMiss']            ?? 0;
        $teenPregnancies  = $data['teenPregnancies']          ?? 0;

        if ($teenPregnancies > 0) {
            $insights[] = "{$n}. ⚠️ **{$teenPregnancies} Adolescent Pregnancy Case(s) Active** — intensify peer-led reproductive counseling and coordinate with local SK/schools for reproductive education.";
            $n++;
        }

        if ($ancRate < 80) {
            $insights[] = "{$n}. 📋 **ANC Compliance is at {$ancRate}%** — below the DOH 80% target. Intensify home visits by BHWs to identify pregnant women who have not yet registered and remind them to complete 4+ prenatal checkups.";
            $n++;
        }

        if ($facilityRate < 90) {
            $insights[] = "{$n}. 🏥 **Facility Delivery Rate is {$facilityRate}%** — encourage expectant mothers to deliver at the RHU Birth Center or city hospital by strengthening referral pathways.";
            $n++;
        }

        if ($highRisk > 0 && $totalPregnant > 0) {
            $pct = round(($highRisk / $totalPregnant) * 100, 1);
            $insights[] = "{$n}. 🚨 **{$highRisk} High-Risk Pregnancies ({$pct}% of active registry)** — schedule immediate follow-up consultations with a midwife or OB-GYN.";
            $n++;
        }

        if ($totalDeaths > 0) {
            $insights[] = "{$n}. 📊 **{$totalDeaths} Maternal Death(s) Logged** — conduct a maternal death audit for each case to review prevention pathways.";
            $n++;
        }

        if (empty($insights)) {
            $insights[] = "✅ All maternal health indicators within target ranges. Continue routine surveillance and SMS checkup reminders.";
        }

        return implode("\n\n", $insights);
    }
}
