<?php

namespace App\Repositories;

use App\Models\MatchingResult;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository for MatchingResult database operations.
 * Centralizes query logic to keep controllers and services clean.
 */
class MatchingResultRepository
{
    /**
     * Get paginated matching results for a specific job.
     *
     * @param int   $jobId
     * @param int   $perPage
     * @param array $filters  Optional filters: ['min_score', 'status', 'sort_by', 'sort_dir']
     * @return LengthAwarePaginator
     */
    public function getByJob(int $jobId, int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = MatchingResult::where('upload_job_id', $jobId)
            ->with(['cv.user', 'uploadJob']);

        // Filter by minimum score
        if (!empty($filters['min_score'])) {
            $query->where('score', '>=', (float) $filters['min_score']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Always sort by score descending for proper ranking
        $query->orderBy('score', 'desc');

        // Paginate first (this returns LengthAwarePaginator)
        $paginatedResults = $query->paginate($perPage);

        // Reassign sequential ranks (1, 2, 3, 4...) without duplicates
        $rank = 1;
        $previousScore = null;
        
        foreach ($paginatedResults as $result) {
            // Only update rank if score is different from previous
            if ($result->score !== $previousScore) {
                $result->rank = $rank;
                $result->save();
            }
            $previousScore = $result->score;
            $rank++;
        }

        return $paginatedResults;
    }

    /**
     * Get the single best candidate for a job.
     */
    public function getTopCandidate(int $jobId): ?MatchingResult
    {
        return MatchingResult::where('upload_job_id', $jobId)
            ->where('status', 'Processed')
            ->orderBy('score', 'desc')
            ->first();
    }

    /**
     * Get top N candidates for a job.
     *
     * @param int $jobId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopCandidates(int $jobId, int $limit = 5)
    {
        return MatchingResult::where('upload_job_id', $jobId)
            ->where('status', 'Processed')
            ->with('cv.user')
            ->orderBy('score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count processed CVs for a job.
     */
    public function countProcessed(int $jobId): int
    {
        return MatchingResult::where('upload_job_id', $jobId)
            ->where('status', 'Processed')
            ->count();
    }

    /**
     * Get score distribution for a job (for charts).
     *
     * @param int $jobId
     * @return array ['0-20' => count, '21-40' => count, ...]
     */
    public function getScoreDistribution(int $jobId): array
    {
        $results = MatchingResult::where('upload_job_id', $jobId)
            ->where('status', 'Processed')
            ->pluck('score');

        $distribution = [
            '0-20'   => 0,
            '21-40'  => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        foreach ($results as $score) {
            if ($score <= 20) $distribution['0-20']++;
            elseif ($score <= 40) $distribution['21-40']++;
            elseif ($score <= 60) $distribution['41-60']++;
            elseif ($score <= 80) $distribution['61-80']++;
            else $distribution['81-100']++;
        }

        return $distribution;
    }
}