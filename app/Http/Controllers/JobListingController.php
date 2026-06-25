<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadJob;

class JobListingController extends Controller
{
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

        // Tetap di halaman Post Job (form kosong lagi), bukan redirect ke Job Listing
        return back()->with('success', 'Job posted successfully!');
    }

    /**
     * Proses screening AI untuk satu job (dipanggil dari modal "Start Screening")
     */
    public function screen(Request $request, $jobId)
    {
        // $job        = UploadJob::findOrFail($jobId);
        // $candidates = Cv::where('upload_job_id', $jobId)->get();
        // ... proses AI matching ...
        // MatchingResult::create([...]);

        return response()->json([
            'success' => true,
            'message' => 'Screening completed',
            'job_id'  => $jobId,
        ]);
    }
}