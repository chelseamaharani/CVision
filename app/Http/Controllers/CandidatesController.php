<?php

namespace App\Http\Controllers;

class CandidatesController extends Controller
{
    /**
     * Tampilkan halaman daftar semua kandidat yang melamar
     */
    public function index()
    {
        // Nanti ganti dengan query database:
        // $candidatesList = Cv::with('user', 'job')->get()->map(function ($cv) {
        //     return [
        //         'id'       => $cv->id,
        //         'name'     => $cv->user->name,
        //         'position' => $cv->job_title,
        //         'cv_path'  => $cv->file_path,
        //     ];
        // });

        return view('pages.candidates');
    }
}