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
     * Proses screening untuk satu job (dipanggil dari modal "Start Screening")
     *
     * SAAT INI: pakai skor random sebagai placeholder, supaya alur Job Listing ->
     * Matching History -> Matching Results -> Candidate Resume bisa berjalan utuh
     * tanpa menunggu model AI.
     *
     * NANTI: ganti bagian "TODO: INTEGRASI AI" di bawah dengan panggilan ke API/model
     * AI temanmu. Yang penting outputnya tetap diisi ke kolom yang sama di MatchingResult.
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

        foreach ($cvs as $cv) {
            // ===================== TODO: INTEGRASI AI =====================
            // Ganti baris-baris di bawah ini dengan hasil asli dari model AI.
            // Kirim $cv->file_path dan $job->required_skills (atau data lain
            // yang dibutuhkan) ke API/model, lalu ambil hasilnya untuk diisi
            // ke variabel-variabel berikut:

            $score           = rand(60, 95);
            $skillsArray     = $job->skills_array;             // dari helper di model UploadJob
            $skillsTotal     = count($skillsArray);
            $skillsCount     = rand(1, max(1, $skillsTotal));
            $skillsMatched   = array_slice($skillsArray, 0, $skillsCount);
            $status          = $score >= 80 ? 'Highly Match' : ($score >= 60 ? 'Good Match' : 'Low Match');
            $similarityScore = round($score / 100, 2);
            $recommendation  = "Candidate shows {$status} based on skill and experience alignment with this position.";

            // ===================== AKHIR TODO =====================

            \App\Models\MatchingResult::updateOrCreate(
                [
                    'upload_job_id' => $job->id,
                    'cv_id'         => $cv->id,
                ],
                [
                    'score'            => $score,
                    'status'           => $status,
                    'skills_matched'   => $skillsMatched,
                    'skills_total'     => $skillsTotal,
                    'skills_count'     => $skillsCount,
                    'skill_gap'        => $skillsTotal > $skillsCount
                        ? implode(', ', array_slice($skillsArray, $skillsCount)) . ' = ' . ($skillsTotal - $skillsCount) . ' skill gap'
                        : 'No skill gap',
                    'experience_years' => $job->min_experience,
                    'education_match'  => $job->education_requirement,
                    'similarity_score' => $similarityScore,
                    'recommendation'   => $recommendation,
                ]
            );
        }

        // Hitung ulang rank berdasarkan score tertinggi
        $results = $job->matchingResults()->get(); // sudah orderByDesc('score') dari relasi
        foreach ($results as $index => $result) {
            $result->update(['rank' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Screening completed',
            'job_id'  => $jobId,
        ]);
    }
}