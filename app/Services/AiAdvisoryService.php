<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Stage 3 advisory AI layer.
 *
 * Cardinal rule: this service NEVER produces, alters, or injects a cost
 * figure into the forecast. It only (a) selects items from an ID list the
 * caller provides, and (b) writes explanatory/advisory text about figures
 * the caller has already computed deterministically. The system prompt
 * below states this explicitly, and callers must not surface any numeric
 * value from these responses as a forecast figure.
 */
class AiAdvisoryService
{
    private const NOT_CONFIGURED = 'AI advisory is not configured. Set ANTHROPIC_API_KEY in .env to enable this feature.';

    private const SYSTEM_PROMPT = <<<'TXT'
        You are an advisory assistant inside a construction cost forecasting tool.
        All arithmetic is performed deterministically elsewhere by the application —
        you must NEVER invent, recalculate, restate-with-changes, or alter any PKR
        cost figure, rate, percentage, or quantity. You may only:
          - select items from an ID list the user provides, and
          - write explanatory or advisory text about figures already given to you.
        If you reference a number, it must be copied verbatim from the provided
        context. Respond only in the format requested, with no markdown code
        fences around JSON responses.
        TXT;

    public function isConfigured(): bool
    {
        return filled(config('services.anthropic.key'));
    }

    /**
     * Ask the AI to select comparable historical projects from a
     * deterministic candidate list and explain why. Every figure shown for
     * a selected project comes from $candidates (built by the caller via
     * plain DB queries) — the AI only returns an id + reason, and any id
     * not present in $candidates is discarded.
     */
    public function findSimilarProjects(array $project, array $candidates): array
    {
        if (!$this->isConfigured()) {
            return ['error' => self::NOT_CONFIGURED];
        }

        $prompt = <<<TXT
            You are helping a Quantity Surveyor find historical projects that are good
            comparables for forecasting the cost of a new project.

            Current project:
            {$this->json($project)}

            Candidate historical projects (each has an id, name, region, gross floor
            area in m2, seating capacity, total rate per m2, and cost per seat where
            available):
            {$this->json($candidates)}

            Select up to 5 candidates that are the best comparables for the current
            project, based on similarity in region, scale (GFA/seating), and building
            profile. For each selection give the project_id (must be one of the ids in
            the candidate list above) and a short reason (1-2 sentences).

            Respond with ONLY a JSON object in this exact shape, no markdown, no extra
            text:
            {"selections":[{"project_id": <id>, "reason": "<reason>"}]}
            TXT;

        try {
            $text = $this->callClaude($prompt, 1024);
        } catch (\Throwable $e) {
            return ['error' => 'AI request failed: '.$e->getMessage()];
        }

        $decoded = $this->decodeJson($text);
        if ($decoded === null || !isset($decoded['selections']) || !is_array($decoded['selections'])) {
            return ['error' => 'AI response could not be parsed.'];
        }

        $candidatesById = [];
        foreach ($candidates as $candidate) {
            $candidatesById[$candidate['id']] = $candidate;
        }

        $selections = [];
        foreach ($decoded['selections'] as $selection) {
            $id = $selection['project_id'] ?? null;
            if ($id !== null && isset($candidatesById[$id])) {
                $selections[] = [
                    'project' => $candidatesById[$id],
                    'reason' => (string) ($selection['reason'] ?? ''),
                ];
            }
        }

        return ['selections' => $selections];
    }

    /**
     * Ask the AI for a brief executive summary of an already-computed
     * forecast — a short headline paragraph, a few key drivers, and the
     * single most important thing to double-check. $context contains only
     * figures the caller has already calculated (Stage 1/2 outputs) — the
     * AI summarizes them, it does not compute anything new.
     */
    public function executiveSummary(array $context): array
    {
        if (!$this->isConfigured()) {
            return ['error' => self::NOT_CONFIGURED];
        }

        $prompt = <<<TXT
            You are writing a brief executive summary for a Quantity Surveyor reviewing
            a construction cost forecast. All figures below were already calculated
            deterministically by the application — do not recalculate, restate with
            different numbers, or invent any new figures. Only refer to numbers that
            appear in the data below, copied verbatim.

            Forecast data and risk signals:
            {$this->json($context)}

            Write a brief executive summary aimed at a QS who wants the headline first
            and detail only on request. Respond with ONLY a JSON object in this exact
            shape, no markdown, no extra text:
            {"summary":"<2-3 sentence headline paragraph covering the overall forecast and confidence>","key_drivers":["<short bullet, max 3 total>"],"top_caveat":"<single most important thing to double-check, referencing only provided figures, or null if nothing notable>"}
            TXT;

        try {
            $text = $this->callClaude($prompt, 512);
        } catch (\Throwable $e) {
            return ['error' => 'AI request failed: '.$e->getMessage()];
        }

        $decoded = $this->decodeJson($text);
        if ($decoded === null || !isset($decoded['summary'])) {
            return ['error' => 'AI response could not be parsed.'];
        }

        return [
            'summary' => (string) $decoded['summary'],
            'key_drivers' => is_array($decoded['key_drivers'] ?? null) ? array_values($decoded['key_drivers']) : [],
            'top_caveat' => $decoded['top_caveat'] ?? null,
        ];
    }

    /**
     * Ask the AI to write a plain-English explanation of an already-computed
     * forecast. $context contains only figures the caller has already
     * calculated (Stage 1/2 outputs) — the AI explains them, it does not
     * compute anything new.
     */
    public function explainForecast(array $context): array
    {
        if (!$this->isConfigured()) {
            return ['error' => self::NOT_CONFIGURED];
        }

        $prompt = <<<TXT
            You are writing a short, plain-English explanation for a Quantity Surveyor
            reviewing a construction cost forecast. All figures below were already
            calculated deterministically by the application — do not recalculate,
            restate with different numbers, or invent any new figures. Only refer to
            numbers that appear in the data below, copied verbatim.

            Forecast data:
            {$this->json($context)}

            Write 3-5 short paragraphs (plain text, no markdown headers) explaining:
            - What the Mid/Low/High figures represent and what is driving the Mid
              value (which estimating elements dominate cost).
            - Why the Low-High band is as wide or narrow as it is (relate to the number
              of comparable projects and the uncertainty multiplier).
            - Where the data is thinnest (low-confidence elements, blended rates, broad
              -spread fallbacks) and what that means for reliability.
            TXT;

        try {
            $text = $this->callClaude($prompt, 1024);
        } catch (\Throwable $e) {
            return ['error' => 'AI request failed: '.$e->getMessage()];
        }

        return ['explanation' => trim($text)];
    }

    /**
     * Ask the AI to flag things a QS should double-check before presenting
     * the forecast to sponsors. $context contains figures and risk signals
     * the caller has already computed — the AI only writes advisory text
     * about them.
     */
    public function senseCheck(array $context): array
    {
        if (!$this->isConfigured()) {
            return ['error' => self::NOT_CONFIGURED];
        }

        $prompt = <<<TXT
            You are sense-checking a construction cost forecast for a Quantity Surveyor
            before it goes to sponsors. All figures and flags below were already
            calculated deterministically by the application — do not recalculate or
            invent any new figures. Base your flags only on the data and flags provided.

            Forecast data and risk signals:
            {$this->json($context)}

            Identify anything the QS should double-check before presenting these
            figures. Respond with ONLY a JSON object in this exact shape, no markdown,
            no extra text:
            {"flags":[{"severity":"high|medium|low","message":"<message, referencing only the provided figures>"}],"summary":"<1-2 sentence overall summary>"}
            If there is nothing notable, return {"flags":[],"summary":"<short reassurance>"}.
            TXT;

        try {
            $text = $this->callClaude($prompt, 1024);
        } catch (\Throwable $e) {
            return ['error' => 'AI request failed: '.$e->getMessage()];
        }

        $decoded = $this->decodeJson($text);
        if ($decoded === null || !isset($decoded['flags']) || !is_array($decoded['flags'])) {
            return ['error' => 'AI response could not be parsed.'];
        }

        return [
            'flags' => $decoded['flags'],
            'summary' => (string) ($decoded['summary'] ?? ''),
        ];
    }

    /**
     * Call the Anthropic Messages API. The API key and model are read from
     * config (which reads from .env) — never hardcoded.
     */
    private function callClaude(string $userMessage, int $maxTokens = 1024): string
    {
        $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => config('services.anthropic.model'),
                'max_tokens' => $maxTokens,
                'system' => self::SYSTEM_PROMPT,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

        $response->throw();

        return (string) $response->json('content.0.text');
    }

    private function json(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function decodeJson(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $text);

        $decoded = json_decode($text, true);

        return is_array($decoded) ? $decoded : null;
    }
}
