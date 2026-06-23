<?php

namespace App\Http\Controllers;

class CandidateResumeController extends Controller
{
    /**
     * Tampilkan detail hasil screening (resume) untuk satu kandidat
     * (Dibuka dari tombol "View Resume" di Matching Results)
     */
    public function show($id)
    {
        // Nanti ganti dengan query database:
        // $result    = MatchingResult::with('candidate', 'job')->findOrFail($id);
        // $candidate = [
        //     'name'       => $result->candidate->name,
        //     'position'   => $result->job->title,
        //     'cv_id'      => $result->candidate->cv_code,
        //     'email'      => $result->candidate->email,
        //     'phone'      => $result->candidate->phone,
        //     'location'   => $result->candidate->location,
        //     'score'      => $result->score,
        //     'rank'       => $result->rank,
        //     ... dst sesuai field yang ada di database
        // ];

        return view('pages.candidate_resume');
    }
}