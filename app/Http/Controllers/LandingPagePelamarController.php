<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UploadJob;

class LandingPagePelamarController extends Controller
{
    /**
     * Tampilkan landing page pelamar (Upload CV + Riwayat CV)
     * Bisa diakses tanpa login. Riwayat CV hanya terisi kalau sudah login.
     */
    public function index()
    {
        $jobs = UploadJob::orderBy('title')->get();

        $riwayatCv = [];

        if (Auth::check()) {
            $riwayatCv = auth()->user()->cvs()
                ->with('uploadJob')
                ->latest()
                ->get();
        }

        return view('pages.landing_page_pelamar', compact('jobs', 'riwayatCv'));
    }

    /**
     * Simpan CV yang diupload (wajib login, dijaga middleware auth di route)
     */
    public function store(Request $request)
    {
        $request->validate([
            'upload_job_id' => 'required|exists:upload_jobs,id',
            'cv_file'       => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        $path = $request->file('cv_file')->store('cv_uploads', 'public');

        auth()->user()->cvs()->create([
            'upload_job_id' => $request->upload_job_id,
            'file_path'     => $path,
            'file_name'     => $request->file('cv_file')->getClientOriginalName(),
        ]);

        return back()->with('success', 'Your CV has been uploaded successfully!');
    }

    /**
     * Hapus CV dari riwayat (wajib login, dijaga middleware auth di route)
     */
    public function destroy($id)
    {
        $cv = auth()->user()->cvs()->findOrFail($id);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($cv->file_path);
        $cv->delete();

        return back()->with('success', 'CV deleted successfully.');
    }
}