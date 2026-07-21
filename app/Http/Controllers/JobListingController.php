<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCVJob;
use App\Models\UploadJob;
use App\Services\AIService;
use App\Services\CVExtractionService;
use App\Services\CVScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobListingController extends Controller
{
    public function __construct(
        private readonly CVScoreService $cvScoreService,
        private readonly AIService $aiService,
    ) {}

    /**
     * Tampilkan halaman Job Listing (daftar posisi + tombol screening)
     */
    public function index()
    {
        $jobsList = UploadJob::withCount('cvs')
            ->latest()
            ->get()
            ->map(function ($job) {
                return [
                    'id'         => $job->id,
                    'title'      => $job->title,
                    'applicants' => $job->cvs_count,
                ];
            });

        return view('pages.job_listing', compact('jobsList'));
    }

    /**
     * Tampilkan form Upload New Job
     */
    public function create()
    {
        return view('pages.upload_job');
    }

    /**
     * Simpan data job baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_title'             => 'required|string|max:255',
            'job_category'          => 'required|string',
            'job_category_other'    => 'required_if:job_category,Others|nullable|string|max:255',
            'job_description'       => 'required|string',
            'required_skills'       => 'required|string',
            'min_experience'        => 'required|string',
            'education_requirement' => 'required|string',
        ]);

        $category = $request->job_category === 'Others'
            ? $request->job_category_other
            : $request->job_category;

        UploadJob::create([
            'user_id'                => auth()->id(),
            'title'                  => $request->job_title,
            'category'               => $category,
            'description'            => $request->job_description,
            'required_skills'        => $request->required_skills,
            'min_experience'         => $request->min_experience,
            'education_requirement'  => $request->education_requirement,
        ]);

        return back()->with('success', 'Job posted successfully!');
    }

    /**
     * Proses screening untuk satu job menggunakan AI Engine (FastAPI)
     * Dipanggil dari modal "Start Screening" di halaman Job Listing
     */
    public function screen(Request $request, $jobId)
    {
        $job = UploadJob::findOrFail($jobId);
        $cvs = $job->cvs;

        if ($cvs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No CVs found for this job yet.',
            ], 422);
        }

        // Cek apakah AI Engine (FastAPI) sedang berjalan
        if (!$this->aiService->isHealthy()) {
            return response()->json([
                'success' => false,
                'message' => 'AI Engine is not running. Please start it first: cd python && uvicorn main:app --reload --port 8000',
            ], 503);
        }

        $processed = 0;
        $failed = 0;
        $totalCvs = $cvs->count();

        foreach ($cvs as $index => $cv) {
            try {
                Log::info("Screening CV {$index}/{$totalCvs}: CV #{$cv->id}");

                ProcessCVJob::dispatchSync($cv);
                $processed++;

                if ($index < $totalCvs - 1) {
                    Log::info("Waiting 5 seconds before processing next CV...");
                    sleep(5);
                }

            } catch (\Throwable $e) {
                Log::error("Screening failed for CV #{$cv->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $results = $job->matchingResults()->get();
        $rankScores = $results->pluck('score', 'id')->toArray();
        arsort($rankScores);
        $rank = 1;
        foreach (array_keys($rankScores) as $id) {
            $results->find($id)->update(['rank' => $rank++]);
        }

        $message = "Screening completed. {$processed} CV(s) processed successfully.";
        if ($failed > 0) {
            $message .= " {$failed} CV(s) failed.";
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'job_id'   => $jobId,
            'stats'    => [
                'processed' => $processed,
                'failed'    => $failed,
                'total'     => $cvs->count(),
            ],
        ]);
    }

    /**
     * Hapus job beserta CV & hasil matching yang terkait
     * Dipanggil dari modal "Delete" di halaman Job Listing
     */
    public function destroy($jobId)
    {
        $job = UploadJob::find($jobId);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found.',
            ], 404);
        }

        $title = $job->title;

        $job->matchingResults()->delete();
        $job->cvs()->delete();
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => "\"{$title}\" has been deleted successfully.",
        ]);
    }
}