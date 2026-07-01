<?php

namespace App\Services;

use App\DTOs\CVScoreResult;
use App\Exceptions\AIProcessingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementation of AIService that communicates with the FastAPI Python backend.
 *
 * Sends CV text and job description to the FastAPI server for:
 * - TF-IDF similarity scoring
 * - SBERT semantic similarity scoring
 * - Hybrid score calculation
 * - Gemini-powered job recommendations
 * - Skill gap analysis
 */
class GeminiAIService implements AIService
{
    /**
     * Base URL of the FastAPI AI Engine server.
     */
    private string $engineUrl;

    /**
     * Request timeout in seconds.
     */
    private int $timeout;

    public function __construct()
    {
        $this->engineUrl = config('services.ai.engine_url', 'http://127.0.0.1:8000');
        $this->timeout = config('services.ai.timeout', 120);
    }

    /**
     * Analyze a CV against a job description via the FastAPI backend.
     *
     * @throws AIProcessingException
     */
    public function analyzeCV(
        string $cvText,
        string $jobDescription,
        array $requiredSkills = [],
        string $jobTitle = 'Unknown Position',
    ): CVScoreResult {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post("{$this->engineUrl}/api/cv/analyze-text", [
                    'cv_text'          => $cvText,
                    'job_description'  => $jobDescription,
                    'required_skills'  => implode(',', $requiredSkills),
                    'job_title'        => $jobTitle,
                ]);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->failed()) {
                Log::error('AI Engine HTTP error', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'duration' => $duration,
                ]);

                throw AIProcessingException::fromHttpResponse(
                    endpoint: '/api/cv/analyze-text',
                    statusCode: $response->status(),
                    body: $response->body(),
                );
            }

            $data = $response->json();

            if (!$data) {
                throw new AIProcessingException('AI Engine returned empty response');
            }

            Log::info('CV analysis completed', [
                'match_percentage' => $data['match_percentage'] ?? null,
                'duration_ms'      => $duration,
            ]);

            return CVScoreResult::fromArray($data);

        } catch (AIProcessingException $e) {
            throw $e;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AI Engine connection failed: ' . $e->getMessage());
            throw new AIProcessingException(
                message: "Cannot connect to AI Engine at {$this->engineUrl}. Is it running?",
                code: 503,
                previous: $e,
            );
        } catch (\Throwable $e) {
            Log::error('Unexpected AI analysis error: ' . $e->getMessage());
            throw new AIProcessingException(
                message: 'Unexpected error during AI analysis: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Check if the FastAPI server is healthy.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->engineUrl}/health");
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}