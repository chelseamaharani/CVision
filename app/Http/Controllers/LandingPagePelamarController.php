<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCVJob;
use App\Models\UploadJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LandingPagePelamarController extends Controller
{
    /**
     * Tampilkan landing page pelamar
     */
    public function index()
    {
        $jobs = UploadJob::orderBy('title')->get();

        $riwayatCv = [];

        if (Auth::check()) {
            $riwayatCv = auth()->user()->cvs()
                ->with('uploadJob', 'matchingResult')
                ->latest()
                ->get();
        }

        return view('pages.landing_page_pelamar', compact('jobs', 'riwayatCv'));
    }

    /**
     * Upload CV dan dispatch AI processing ke queue
     */
    public function store(Request $request)
    {
        $request->validate([
            'upload_job_id' => 'required|exists:upload_jobs,id',
            'cv_file'       => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            // Save the uploaded file
            $path = $request->file('cv_file')->store('cv_uploads', 'public');

            // Create CV record
            $cv = auth()->user()->cvs()->create([
                'upload_job_id' => $request->upload_job_id,
                'file_path'     => $path,
                'file_name'     => $request->file('cv_file')->getClientOriginalName(),
            ]);

            // Dispatch async AI processing job
            ProcessCVJob::dispatch($cv);

            Log::info("CV #{$cv->id} uploaded and queued for AI processing", [
                'job_id' => $request->upload_job_id,
                'file'   => $cv->file_name,
            ]);

            return back()->with('success', 'CV uploaded successfully! AI analysis is in progress. Please check back later for results.');

        } catch (\Throwable $e) {
            Log::error('CV upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to upload CV. Please try again.');
        }
    }

    /**
     * Hapus CV
     */
    public function destroy($id)
    {
        $cv = auth()->user()->cvs()->findOrFail($id);

        // Hapus matching result jika ada
        if ($cv->matchingResult) {
            $cv->matchingResult->delete();
        }

        // Hapus file
        \Illuminate\Support\Facades\Storage::disk('public')->delete($cv->file_path);

        $cv->delete();

        return back()->with('success', 'CV deleted successfully.');
    }
}
