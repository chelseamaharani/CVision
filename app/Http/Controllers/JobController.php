<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
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

        // Tentukan kategori final (jika Others, pakai input manual)
        $category = $request->job_category === 'Others'
            ? $request->job_category_other
            : $request->job_category;

        // Simpan ke database (sesuaikan dengan model kamu)
        // Job::create([
        //     'title'             => $request->job_title,
        //     'category'          => $category,
        //     'description'       => $request->job_description,
        //     'required_skills'   => $request->required_skills,
        //     'min_experience'    => $request->min_experience,
        //     'education'         => $request->education_requirement,
        // ]);

        return redirect()->route('jobs.index')->with('success', 'Job berhasil diupload!');
    }

    /**
     * Tampilkan daftar job
     */
    public function index()
    {
        return view('pages.upload_job'); // sesuaikan dengan halaman listing kamu
    }
}