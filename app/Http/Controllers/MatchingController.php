<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Tampilkan halaman Matching History (daftar posisi yang sudah di-screening)
     */
    public function index()
    {
        // Nanti ganti dengan query database:
        // $historyList = Job::whereHas('matchingResults')->withCount('applicants')->get();

        return view('pages.matching_history');
    }

    /**
     * Tampilkan hasil matching/ranking untuk satu job tertentu
     * (Dibuka dari tombol "View Results" di Matching History)
     */
    public function results(Request $request)
    {
        $jobId = $request->query('job_id');

        // Nanti ganti dengan query database:
        // $job        = Job::findOrFail($jobId);
        // $candidates = Candidate::withMatchingScore($jobId)->orderByDesc('score')->take(5)->get();
        // $stats = [
        //     'matches'    => $candidates->count(),
        //     'candidates' => Candidate::where('job_id', $jobId)->count(),
        //     'accuracy'   => $candidates->avg('score') . '%',
        //     'date'       => now()->format('M d, Y'),
        // ];

        return view('pages.matching_results');
    }
}