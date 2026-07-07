<?php

namespace App\Services;

use App\DTOs\CVScoreResult;
use App\Exceptions\AIProcessingException;
use App\Models\Cv;
use App\Models\MatchingResult;
use App\Models\UploadJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates the full CV scoring workflow:
 * 1. Extract text from CV file
 * 2. Send to AI service for analysis
 * 3. Cache results for duplicate prevention
 * 4. Save results to database
 */
class CVScoreService
{
    public function __construct(
        private readonly CVExtractionService $extractionService,
        private readonly AIService $aiService,
    ) {}

    /**
     * Analyze a CV against its associated job and save the result.
     *
     * @param Cv $cv The CV model instance
     * @return CVScoreResult
     *
     * @throws AIProcessingException
     * @throws \RuntimeException
     */
    public function analyze(Cv $cv): CVScoreResult
    {
        $job = $cv->uploadJob;

        if (!$job) {
            throw new \RuntimeException("CV #{$cv->id} has no associated job");
        }

        // Check cache first (same CV file + same job = same result)
        $cacheKey = $this->buildCacheKey($cv, $job);
        $cached = Cache::get($cacheKey);
        if ($cached instanceof CVScoreResult) {
            Log::info("Using cached analysis for CV #{$cv->id}");
            $this->saveResult($cv, $cached);
            return $cached;
        }

        // Step 1: Extract text from CV file
        Log::info("Extracting text from CV #{$cv->id}");
        $cvText = $this->extractionService->extract($cv->file_path);

        if (empty(trim($cvText))) {
            throw new \RuntimeException("No text could be extracted from CV #{$cv->id}");
        }

        // Clean UTF-8 encoding to prevent json_encode errors
        $cvText = mb_convert_encoding($cvText, 'UTF-8', 'UTF-8');

        // Step 2: Analyze via AI service
        Log::info("Analyzing CV #{$cv->id} against job #{$job->id}");
        $result = $this->aiService->analyzeCV(
            cvText: $cvText,
            jobDescription: $job->description,
            requiredSkills: $job->skills_array,
            jobTitle: $job->title,
            minExperienceYears: $job->min_experience ? (float) $job->min_experience : null,
            requiredEducation: $job->education_requirement ?: null,
        );

        // Step 3: Cache the result (TTL: 1 hour)
        Cache::put($cacheKey, $result, now()->addHour());

        // Step 4: Save to database
        $this->saveResult($cv, $result, $job);

        Log::info("CV #{$cv->id} analysis complete. Score: {$result->matchPercentage}%");

        return $result;
    }

    /**
     * Save analysis result to the matching_results table.
     */
    private function saveResult(Cv $cv, CVScoreResult $result, ?UploadJob $job = null): MatchingResult
    {
        $job = $job ?? $cv->uploadJob;

        $data = array_merge($result->toArray(), [
            'upload_job_id'   => $job->id,
            'cv_id'           => $cv->id,
            'status'          => 'Processed',
            'skills_matched'  => $result->skillGap['skills_present'] ?? null,
            'skill_gap'       => $result->skillGap['skills_missing'] ?? null,
            'experience_years'=> $result->experienceYears,
            'education_match' => $result->educationLevel,
            'rank'            => $this->calculateRank($job->id, $result->matchPercentage),
        ]);

        // Update existing or create new
        return MatchingResult::updateOrCreate(
            ['cv_id' => $cv->id, 'upload_job_id' => $job->id],
            $data
        );
    }

    /**
     * Build a unique cache key for a CV + job combination.
     * Uses file modification time to invalidate cache on re-upload.
     */
    private function buildCacheKey(Cv $cv, UploadJob $job): string
    {
        $fileTimestamp = $cv->updated_at?->timestamp ?? $cv->created_at?->timestamp ?? time();
        return "cv_analysis:{$cv->id}:job_{$job->id}:{$fileTimestamp}";
    }

    /**
     * Calculate the rank of this score among all results for the same job.
     */
    private function calculateRank(int $jobId, float $score): int
    {
        $higherScores = MatchingResult::where('upload_job_id', $jobId)
            ->where('score', '>', $score)
            ->count();

        return $higherScores + 1;
    }
}