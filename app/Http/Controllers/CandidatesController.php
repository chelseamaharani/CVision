<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                    'email'    => $cv->user->email ?? '-',
                    'position' => $cv->uploadJob->title ?? 'Unknown Position',
                    'cv_path'  => $cv->file_path,
                ];
            });

        return view('pages.candidates', compact('candidatesList'));
    }

    /**
     * Hapus data kandidat (CV, user, matching results)
     */
    public function destroy($id)
    {
        try {
            $cv = Cv::with('user')->findOrFail($id);

            // Hapus file CV dari storage
            if ($cv->file_path && Storage::exists($cv->file_path)) {
                Storage::delete($cv->file_path);
                Log::info("CV file deleted: {$cv->file_path}");
            }

            // Hapus matching results terkait
            $cv->matchingResult()->delete();

            // Hapus data CV
            $cv->delete();

            Log::info("Candidate #{$id} deleted successfully");

            return response()->json([
                'success' => true,
                'message' => 'Kandidat berhasil dihapus.',
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to delete candidate #{$id}: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kandidat: ' . $e->getMessage(),
            ], 500);
        }
    }
}
