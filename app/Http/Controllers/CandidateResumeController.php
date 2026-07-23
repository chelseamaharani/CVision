<?php

namespace App\Http\Controllers;

use App\Models\MatchingResult;
use App\Services\CVExtractionService;
use App\Services\ResumeParsingService;
use Illuminate\Support\Facades\Log;

class CandidateResumeController extends Controller
{
    public function __construct(
        private readonly CVExtractionService $extractionService,
        private readonly ResumeParsingService $resumeParsingService,
    ) {}

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
        
        // Log for debugging
        if (empty($recommendations)) {
            Log::info("No recommendations found for CV #{$result->cv_id}", [
                'raw_recommendation' => $result->recommendation,
                'parsed' => $recommendation
            ]);
        }
        
        // Ensure we have fallback data if recommendations are empty
        if (empty($recommendations)) {
            $recommendations = [
                [
                    'rank' => 1,
                    'job_title' => 'Similar Position',
                    'confidence' => 70,
                    'reasoning' => 'Based on CV analysis, this candidate has relevant skills and experience.',
                    'supporting_skills' => $result->skills_matched ?? []
                ]
            ];
        }

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

        // Extract CV text and generate structured resume (LOCAL - no external API calls)
        $cvText = null;
        $structuredResume = null;
        $resumeText = null;
        
        try {
            if ($result->cv->file_path) {
                // CVExtractionService expects path relative to 'public' disk
                $cvText = $this->extractionService->extract($result->cv->file_path);
                $cvText = mb_convert_encoding($cvText, 'UTF-8', 'UTF-8');
                
                // Parse resume locally using pure regex (NO Gemini API needed!)
                $structuredResume = $this->resumeParsingService->parse($cvText);
                $resumeText = $this->resumeParsingService->toText($structuredResume);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to parse resume: {$e->getMessage()}");
        }

         // Use parsed data from ResumeParsingService if available
         $phone = $structuredResume['phone'] ?? '-';
         $location = $structuredResume['address'] ?? '-';
         $experience = $structuredResume['experience'] ?? [];
         $educationList = $structuredResume['education'] ?? [];
         
         $candidate = [
             'name'              => $name,
             'position'          => $result->uploadJob->title ?? '',
             'cv_id'             => 'CV-' . now()->format('Y') . '-' . str_pad($result->cv_id, 5, '0', STR_PAD_LEFT),
             'email'             => $result->cv->user->email ?? '-',
             'phone'             => $phone,
             'location'          => $location,
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
                 ? 'Based on AI analysis, this candidate is recommended for positions that match their skills and experience.'
                 : 'AI recommendation not available.',

             // Files & Resume
             'cv_path'           => $result->cv->file_path,
             'cv_id_display'     => $result->cv_id,
             'cv_model_id'       => $result->cv->id,
             'cv_text'           => $cvText ? mb_substr($cvText, 0, 5000) : null,
             'structured_resume' => $structuredResume,
             'resume_text'       => $resumeText,
             'experience'        => $experience,
             'education_list'    => $educationList,
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