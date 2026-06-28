<?php

namespace App\Http\Controllers;

use App\Models\Cv;

class CandidatesController extends Controller
{
    /**
     * Tampilkan halaman daftar semua kandidat yang melamar
     */
    public function index()
    {
        $candidatesList = Cv::with('user', 'uploadJob')
            ->latest()
            ->get()
            ->map(function ($cv) {
                return [
                    'id'       => $cv->id,
                    'name'     => $cv->user->name ?? 'Unknown',
                    'position' => $cv->uploadJob->title ?? 'Unknown Position',
                    'cv_path'  => $cv->file_path,
                ];
            });

        return view('pages.candidates', compact('candidatesList'));
    }
}