<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadJob;

class MatchingController extends Controller
{
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

        $matchingResults = $job->matchingResults()->with('cv.user')->get();

        $candidates = $matchingResults->map(function ($result) {
            $name = $result->cv->user->name ?? 'Unknown';

            return [
                'rank'     => $result->rank,
                'initials' => strtoupper(substr($name, 0, 1)) . strtoupper(substr(strrchr($name, ' ') ?: '', 1, 1)),
                'name'     => $name,
                'role'     => $result->cv->uploadJob->title ?? '',
                'score'    => $result->score,
                'top'      => $result->rank === 1,
                'skills'   => $result->skills_matched ?? [],
                'matching_result_id' => $result->id,
            ];
        });

        $stats = [
            'matches'    => $matchingResults->count(),
            'candidates' => $job->cvs_count ?? $job->cvs()->count(),
            'accuracy'   => $matchingResults->count() ? round($matchingResults->avg('score')) . '%' : '0%',
            'date'       => $matchingResults->first()?->created_at?->format('M d, Y') ?? now()->format('M d, Y'),
        ];

        return view('pages.matching_results', [
            'job'        => $job,
            'candidates' => $candidates,
            'stats'      => $stats,
        ]);
    }
}