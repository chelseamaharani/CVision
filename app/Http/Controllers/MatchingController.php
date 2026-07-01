<?php

namespace App\Http\Controllers;

use App\Models\MatchingResult;
use App\Models\UploadJob;
use App\Repositories\MatchingResultRepository;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function __construct(
        private readonly MatchingResultRepository $matchingResultRepo,
    ) {}

    /**
     * Tampilkan halaman Matching History
     * (daftar job yang sudah pernah di-screening, beserta jumlah pelamarnya)
     */
    public function index()
    {
        $historyList = UploadJob::whereHas('matchingResults')
            ->withCount('cvs')
            ->latest()
            ->get()
            ->map(function ($job) {
                return [
                    'id'         => $job->id,
                    'title'      => $job->title,
                    'applicants' => $job->cvs_count,
                ];
            });

        return view('pages.matching_history', compact('historyList'));
    }

    /**
     * Tampilkan hasil matching/ranking untuk satu job tertentu
     * (Dibuka dari tombol "View Results" di Matching History)
     */
    public function results(Request $request)
    {
        $jobId = $request->query('job_id');
        $job   = UploadJob::findOrFail($jobId);

        // Use repository to get paginated, sorted results
        $filters = $request->only(['min_score', 'status', 'sort_by', 'sort_dir']);
        $matchingResults = $this->matchingResultRepo->getByJob($jobId, 50, $filters);

        $candidates = $matchingResults->map(function ($result) {
            $name = $result->cv->user->name ?? 'Unknown';
            $initials = '';
            $parts = explode(' ', $name);
            foreach ($parts as $part) {
                if (!empty(trim($part))) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }

            // Parse recommendation JSON
            $recommendation = json_decode($result->recommendation ?? '{}', true);
            $topJob = $recommendation['recommendations'][0]['job_title'] ?? '';

            return [
                'id'               => $result->id,
                'rank'             => $result->rank,
                'initials'         => substr($initials, 0, 2) ?: '?',
                'name'             => $name,
                'email'            => $result->cv->user->email ?? '-',
                'role'             => $result->cv->uploadJob->title ?? '',
                'score'            => $result->score,
                'top'              => $result->rank === 1,
                'skills_matched'   => $result->skills_matched ?? [],
                'skill_gap'        => $result->skill_gap ?? [],
                'experience_years' => $result->experience_years,
                'education_match'  => $result->education_match,
                'tfidf_score'      => $result->tfidf_score,
                'sbert_score'      => $result->sbert_score,
                'hybrid_score'     => $result->hybrid_score,
                'recommended_job'  => $topJob,
                'matching_result_id' => $result->id,
                'status'           => $result->status,
                'created_at'       => $result->created_at?->format('M d, Y'),
            ];
        });

        // Calculate stats from real data
        $scores = $matchingResults->pluck('score')->filter();
        $avgScore = $scores->count() > 0 ? round($scores->avg()) : 0;
        $topScore = $scores->max() ?? 0;

        $stats = [
            'matches'    => $matchingResults->count(),
            'candidates' => $job->cvs()->count(),
            'accuracy'   => $avgScore . '%',
            'top_score'  => $topScore . '%',
            'date'       => $matchingResults->first()?->created_at?->format('M d, Y') ?? now()->format('M d, Y'),
        ];

        // Score distribution for chart
        $distribution = $this->matchingResultRepo->getScoreDistribution($jobId);

        return view('pages.matching_results', [
            'job'           => $job,
            'candidates'    => $candidates,
            'stats'         => $stats,
            'distribution'  => $distribution,
            'filters'       => $filters,
        ]);
    }
}