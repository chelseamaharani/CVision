<?php

namespace App\Http\Controllers;

use App\Models\MatchingResult;

class CandidateResumeController extends Controller
{
    /**
     * Tampilkan detail hasil screening (resume) untuk satu kandidat
     * (Dibuka dari tombol "View Resume" di Matching Results)
     */
    public function show($id)
    {
        $result = MatchingResult::with('cv.user', 'uploadJob')->findOrFail($id);

        $name = $result->cv->user->name ?? 'Unknown';

        // Parse recommendation JSON
        $recommendation = json_decode($result->recommendation ?? '{}', true);
        $recommendations = $recommendation['recommendations'] ?? [];

        // Determine status label based on score
        $status = $result->status ?? $this->getStatusLabel($result->score);

        // Calculate percentile
        $totalResults = MatchingResult::where('upload_job_id', $result->upload_job_id)->count();
        $betterResults = MatchingResult::where('upload_job_id', $result->upload_job_id)
            ->where('score', '>', $result->score)
            ->count();
        $percentile = $totalResults > 0
            ? 'Top ' . round((($totalResults - $betterResults) / $totalResults) * 100) . '%'
            : '';

        $candidate = [
            'name'              => $name,
            'position'          => $result->uploadJob->title ?? '',
            'cv_id'             => 'CV-' . now()->format('Y') . '-' . str_pad($result->cv_id, 5, '0', STR_PAD_LEFT),
            'email'             => $result->cv->user->email ?? '-',
            'phone'             => '-',
            'location'          => '-',
            'score'             => $result->score,
            'rank'              => $result->rank,
            'status'            => $status,
            'percentile'        => $percentile,

            // AI Analysis Results
            'skills_matched'    => $result->skills_matched ?? [],
            'skills_total'      => $result->skills_total ?? count($result->skills_matched ?? []) + count($result->skill_gap ?? []),
            'skills_count'      => $result->skills_count ?? count($result->skills_matched ?? []),
            'skill_gap'         => is_array($result->skill_gap)
                ? implode(', ', $result->skill_gap)
                : ($result->skill_gap ?? ''),
            'experience_years'  => $result->experience_years ? $result->experience_years . ' Years' : 'N/A',
            'education'         => $result->education_match ?? 'Unknown',

            // Detailed AI Scores
            'tfidf_score'       => $result->tfidf_score,
            'sbert_score'       => $result->sbert_score,
            'hybrid_score'      => $result->hybrid_score,
            'similarity'        => $result->similarity_score ?? $result->hybrid_score,

            // Recommendation
            'recommendations'   => $recommendations,
            'recommendation'    => !empty($recommendations)
                ? 'Recommended for: ' . collect($recommendations)->pluck('job_title')->take(3)->implode(', ')
                : 'AI recommendation not available.',

            // Files
            'cv_path'           => $result->cv->file_path,
            'cv_id_display'     => $result->cv_id,
            'experience'        => [],
        ];

        return view('pages.candidate_resume', compact('candidate'));
    }

    /**
     * Get status label based on score range.
     */
    private function getStatusLabel(?float $score): string
    {
        if ($score === null) return 'Pending';
        if ($score >= 85) return 'Highly Match';
        if ($score >= 70) return 'Good Match';
        if ($score >= 50) return 'Fair Match';
        return 'Low Match';
    }
}
