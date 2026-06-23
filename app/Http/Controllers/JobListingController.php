<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobListingController extends Controller
{
    /**
     * Tampilkan halaman Job Listing (daftar posisi + tombol screening)
     */
    public function index()
    {
        // Nanti ganti dengan query database:
        // $jobsList = Job::withCount('applicants')->get();

        return view('pages.job_listing');
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

        // Job::create([
        //     'title'             => $request->job_title,
        //     'category'          => $category,
        //     'description'       => $request->job_description,
        //     'required_skills'   => $request->required_skills,
        //     'min_experience'    => $request->min_experience,
        //     'education'         => $request->education_requirement,
        // ]);

        return redirect()->route('job_listing.index')->with('success', 'Job posted successfully!');
    }

    /**
     * Proses screening AI untuk satu job (dipanggil dari modal "Start Screening")
     */
    public function screen(Request $request, $jobId)
    {
        // $job        = Job::findOrFail($jobId);
        // $candidates = Cv::where('job_id', $jobId)->get();
        // ... proses AI matching ...
        // MatchingResult::create([...]);

        return response()->json([
            'success' => true,
            'message' => 'Screening completed',
            'job_id'  => $jobId,
        ]);
    }
}