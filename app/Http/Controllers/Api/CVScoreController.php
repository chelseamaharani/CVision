<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\MatchingResult;
use App\Services\CVScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * REST API controller for CV analysis.
 * Provides endpoints for external integrations and programmatic access.
 */
class CVScoreController extends Controller
{
    public function __construct(
        private readonly CVScoreService $cvScoreService,
    ) {}

    /**
     * Analyze a specific CV by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function analyze(int $id): JsonResponse
    {
        try {
            $cv = Cv::with('uploadJob')->findOrFail($id);

            if (!$cv->uploadJob) {
                return response()->json([
                    'error' => 'CV has no associated job',
                ], 422);
            }

            $result = $this->cvScoreService->analyze($cv);

            return response()->json([
                'success' => true,
                'data'    => [
                    'cv_id'            => $cv->id,
                    'job_id'           => $cv->upload_job_id,
                    'job_title'        => $cv->uploadJob->title,
                    'tfidf_score'      => $result->tfidfScore,
                    'sbert_score'      => $result->sbertScore,
                    'hybrid_score'     => $result->hybridScore,
                    'match_percentage' => $result->matchPercentage,
                    'recommendation'   => $result->recommendation,
                    'skill_gap'        => $result->skillGap,
                    'experience_years' => $result->experienceYears,
                    'education_level'  => $result->educationLevel,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'CV not found',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('API CV analysis failed: ' . $e->getMessage());

            return response()->json([
                'error'   => 'Analysis failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the analysis result for a specific CV.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function result(int $id): JsonResponse
    {
        try {
            $cv = Cv::with(['uploadJob', 'matchingResult'])->findOrFail($id);

            if (!$cv->matchingResult) {
                return response()->json([
                    'success' => true,
                    'data'    => [
                        'cv_id'  => $cv->id,
                        'status' => 'Pending',
                        'message' => 'CV is still being processed or has not been analyzed yet.',
                    ],
                ]);
            }

            $result = $cv->matchingResult;

            return response()->json([
                'success' => true,
                'data'    => [
                    'cv_id'            => $cv->id,
                    'job_id'           => $cv->upload_job_id,
                    'job_title'        => $cv->uploadJob?->title,
                    'status'           => $result->status,
                    'score'            => $result->score,
                    'similarity_score' => $result->similarity_score,
                    'tfidf_score'      => $result->tfidf_score,
                    'sbert_score'      => $result->sbert_score,
                    'hybrid_score'     => $result->hybrid_score,
                    'rank'             => $result->rank,
                    'skills_matched'   => $result->skills_matched,
                    'skill_gap'        => $result->skill_gap,
                    'experience_years' => $result->experience_years,
                    'education_match'  => $result->education_match,
                    'recommendation'   => json_decode($result->recommendation ?? '{}', true),
                    'analyzed_at'      => $result->created_at,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'CV not found'], 404);
        } catch (\Throwable $e) {
            Log::error('API result fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch result'], 500);
        }
    }

    /**
     * List all analysis results for a specific job.
     *
     * @param int $jobId
     * @return JsonResponse
     */
    public function jobResults(int $jobId): JsonResponse
    {
        try {
            $results = MatchingResult::where('upload_job_id', $jobId)
                ->with('cv.user')
                ->orderBy('score', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data'    => $results->items(),
                'meta'    => [
                    'current_page' => $results->currentPage(),
                    'last_page'    => $results->lastPage(),
                    'total'        => $results->total(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('API job results fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch results'], 500);
        }
    }
}