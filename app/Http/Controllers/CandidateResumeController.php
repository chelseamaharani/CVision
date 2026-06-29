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

        $candidate = [
            'name'             => $name,
            'position'         => $result->uploadJob->title ?? '',
            'cv_id'            => 'CV-' . now()->format('Y') . '-' . str_pad($result->cv_id, 5, '0', STR_PAD_LEFT),
            'email'            => $result->cv->user->email ?? '-',
            'phone'            => '-', // Belum ada sumbernya — nanti diisi otomatis oleh AI dari hasil parsing CV
            'location'         => '-', // Belum ada sumbernya — nanti diisi otomatis oleh AI dari hasil parsing CV
            'score'            => $result->score,
            'rank'             => $result->rank,
            'status'           => $result->status,
            'percentile'       => '',
            'skills_matched'   => $result->skills_matched ?? [],
            'skills_total'     => $result->skills_total ?? 0,
            'skills_count'     => $result->skills_count ?? 0,
            'skill_gap'        => $result->skill_gap,
            'experience_years' => $result->experience_years,
            'education'        => $result->education_match,
            'similarity'       => $result->similarity_score,
            'cv_path'          => $result->cv->file_path,
            'experience'       => [], // belum ada sumber data riwayat pekerjaan terstruktur
            'recommendation'   => $result->recommendation,
        ];

        return view('pages.candidate_resume', compact('candidate'));
    }
}