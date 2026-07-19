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
        $cvText = mb_convert_encoding($cvText, 'UTF-8', 'auto');

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

        // Fallback: if Gemini returns empty recommendations, generate based on skills
        if (empty($result->recommendation['recommendations'] ?? [])) {
            Log::info("Gemini returned empty recommendations, using skill-based fallback");
            $result = $this->addFallbackRecommendations($result, $job, $cvText);
        }

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

        $skillsPresent = $result->skillGap['skills_present'] ?? [];
        $skillsMissing = $result->skillGap['skills_missing'] ?? [];

        $data = array_merge($result->toArray(), [
            'upload_job_id'   => $job->id,
            'cv_id'           => $cv->id,
            'status'          => 'Processed',
            'skills_matched'  => $skillsPresent,
            'skill_gap'       => $skillsMissing,
            'skills_total'    => count($skillsPresent) + count($skillsMissing),
            'skills_count'    => count($skillsPresent),
            'experience_years'=> $result->experienceYears,
            'education_match' => $result->educationLevel,
            'rank'            => 0, // Will be calculated after save
        ]);

        // Update existing or create new
        $matchingResult = MatchingResult::updateOrCreate(
            ['cv_id' => $cv->id, 'upload_job_id' => $job->id],
            $data
        );

        // Calculate rank AFTER data is saved to ensure accuracy
        $matchingResult->rank = MatchingResult::where('upload_job_id', $job->id)
            ->where('score', '>', $matchingResult->score)
            ->count() + 1;
        $matchingResult->save();

        return $matchingResult;
    }

    /**
     * Build a unique cache key for a CV + job combination.
     * Uses file modification time to invalidate cache on re-upload.
     */
    private function buildCacheKey(Cv $cv, UploadJob $job): string
    {
        $fileTimestamp = $cv->updated_at?->timestamp ?? $cv->created_at?->timestamp ?? time();
        $jobTimestamp = $job->updated_at?->timestamp ?? $job->created_at?->timestamp ?? time();
        return "cv_analysis:{$cv->id}:job_{$job->id}:{$fileTimestamp}:{$jobTimestamp}";
    }

    /**
     * Add fallback recommendations based on skills if Gemini returns empty.
     */
    private function addFallbackRecommendations(CVScoreResult $result, UploadJob $job, string $cvText): CVScoreResult
    {
        $requiredSkills = $job->skills_array;
        $matchedSkills = $result->skillGap['skills_present'] ?? [];
        
        // Generate simple recommendations based on matched skills
        $recommendations = [];
        
        if (!empty($matchedSkills)) {
            $recommendations[] = [
                'job_title' => $job->title . ' (Match)',
                'confidence' => round($result->matchPercentage),
                'reasoning' => 'Based on skill matching: ' . implode(', ', array_slice($matchedSkills, 0, 3)),
                'supporting_skills' => array_slice($matchedSkills, 0, 5),
            ];
        }
        
        // Add generic recommendations based on experience
        $experienceYears = $result->experienceYears;
        if ($experienceYears > 0) {
            $recommendations[] = [
                'job_title' => 'Related Positions',
                'confidence' => max(50, round($result->matchPercentage)),
                'reasoning' => "Based on {$experienceYears} years of experience",
                'supporting_skills' => $matchedSkills,
            ];
        }
        
        // If still empty, add at least one generic recommendation
        if (empty($recommendations)) {
            $recommendations[] = [
                'job_title' => 'General Positions',
                'confidence' => 50,
                'reasoning' => 'Based on CV content analysis',
                'supporting_skills' => [],
            ];
        }
        
        // Create new result with fallback recommendations
        $newResult = new CVScoreResult(
            tfidfScore: $result->tfidfScore,
            sbertScore: $result->sbertScore,
            hybridScore: $result->hybridScore,
            matchPercentage: $result->matchPercentage,
            recommendation: ['recommendations' => $recommendations],
            skillGap: $result->skillGap,
            experienceYears: $result->experienceYears,
            educationLevel: $result->educationLevel,
            minExperienceYears: $result->minExperienceYears,
            requiredEducation: $result->requiredEducation,
        );
        
        return $newResult;
    }

}
