<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
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
     * Generate smart health insights from aggregated analytics data.
     * Results are cached for 6 hours to avoid redundant API calls.
     */
    public function generateInsights(array $analyticsData): string
    {
        // Per policy: use only rule-based decision support and do not call external AI services.
        // Always use the local, rule-based fallback insights implementation.
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

    // ── Prompt Builders ───────────────────────────────────────────────────────

    private function buildInsightPrompt(array $data): string
    {
        $statsJson = json_encode($data, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a maternal and child health advisor for a city health office in the Philippines.
You are assisting a City Health Officer (CHO) review their health program performance.

Based on the following city-wide health statistics, provide exactly 5 actionable, specific recommendations.
Focus on: maternal mortality reduction, antenatal care (ANC) compliance, facility delivery rates, and high-risk pregnancy management.
Use professional but simple language suitable for Philippine public health context.
Format your response as a numbered list (1. 2. 3. 4. 5.).
Be specific and practical. Reference the actual numbers where relevant.

Current Health Statistics:
{$statsJson}

Provide 5 recommendations:
PROMPT;
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
If the question is unrelated to maternal/child health, politely redirect to relevant health topics.
PROMPT;
    }

    // ── Gemini API Call ───────────────────────────────────────────────────────

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
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ],
            ]);

            if ($response->failed()) {
                Log::error('AIInsightService: Gemini API returned error: ' . $response->body());
                return "⚠️ Unable to generate AI insights at this time. Please try again later.";
            }

            $body = $response->json();
            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($text)) {
                Log::warning('AIInsightService: Gemini returned empty response. Body: ' . $response->body());
                return "⚠️ AI insights are unavailable at this time. Please check back later.";
            }

            return trim($text);

        } catch (\Throwable $e) {
            Log::error('AIInsightService: Exception calling Gemini API: ' . $e->getMessage());
            return "⚠️ AI insights encountered an error: " . $e->getMessage();
        }
    }

    // ── Fallback (no API key configured) ─────────────────────────────────────

    /**
     * Rule-based fallback when Gemini API key is not set.
     * Returns practical recommendations based on the actual data values.
     */
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

        if ($ancRate < 80) {
            $insights[] = "{$n}. 📋 **ANC Compliance is at {$ancRate}%** — below the DOH 80% target. Intensify home visits by BHWs to identify pregnant women who have not yet registered and remind them to complete 4+ prenatal checkups.";
            $n++;
        }

        if ($facilityRate < 90) {
            $insights[] = "{$n}. 🏥 **Facility Delivery Rate is {$facilityRate}%** — encourage expectant mothers to deliver at the RHU Birth Center or city hospital by strengthening referral pathways and providing transport assistance through PhilHealth/4Ps programs.";
            $n++;
        }

        if ($highRisk > 0 && $totalPregnant > 0) {
            $pct = round(($highRisk / $totalPregnant) * 100, 1);
            $insights[] = "{$n}. 🚨 **{$highRisk} high-risk pregnancies ({$pct}% of active pregnancies)** — schedule immediate follow-up consultations with a midwife or OB-GYN. Prioritize those with hypertension or late pregnancy with no recent checkup.";
            $n++;
        }

        if ($totalDeaths > 0) {
            $insights[] = "{$n}. 📊 **{$totalDeaths} maternal death(s) recorded** — conduct a maternal death audit for each case. Identify whether deaths were preventable through earlier detection, better ANC, or timely referral. Share findings at the next CHO meeting.";
            $n++;
        }

        if ($totalNearMiss > 0) {
            $insights[] = "{$n}. ⚠️ **{$totalNearMiss} near-miss morbidity case(s)** — near-miss cases are strong indicators of systemic gaps. Review each case's complication type and implement targeted interventions (e.g., MgSO4 availability for eclampsia, blood bank access for hemorrhage).";
            $n++;
        }

        if (empty($insights)) {
            $insights[] = "✅ No critical health concerns detected based on current data. Continue routine monitoring and maintain ANC and facility delivery programs. Consider implementing proactive SMS reminders for all pregnant women approaching their third trimester.";
        }

        return implode("\n\n", $insights) . "\n\n---\n_💡 Configure your GEMINI_API_KEY in `.env` to enable advanced AI-powered insights._";
    }
}
