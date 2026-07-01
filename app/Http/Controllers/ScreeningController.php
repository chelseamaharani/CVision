<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCVJob;
use App\Models\Cv;
use App\Models\UploadJob;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScreeningController extends Controller
{
    public function __construct(
        private readonly AIService $aiService,
    ) {}

    /**
     * Tampilkan daftar CV untuk satu job (screening per CV)
     */
    public function index(Request $request, $jobId)
    {
        $job = UploadJob::withCount('cvs')->findOrFail($jobId);

        $cvs = Cv::where('upload_job_id', $jobId)
            ->with(['user', 'matchingResult'])
            ->latest()
            ->get()
            ->map(function ($cv) {
                $result = $cv->matchingResult;
                return [
                    'id'              => $cv->id,
                    'name'            => $cv->user->name ?? 'Unknown',
                    'email'           => $cv->user->email ?? '-',
                    'file_name'       => $cv->file_name,
                    'file_path'       => $cv->file_path,
                    'uploaded_at'     => $cv->created_at->format('M d, Y H:i'),
                    'status'          => $result?->status ?? 'Pending',
                    'score'           => $result?->score,
                    'rank'            => $result?->rank,
                    'has_result'      => $result !== null,
                ];
            });

        return view('pages.screening_cvs', compact('job', 'cvs'));
    }

    /**
     * Screening SATU CV tertentu via AJAX
     */
    public function screenSingle(Request $request, $cvId)
    {
        try {
            $cv = Cv::with('uploadJob')->findOrFail($cvId);

            // Cek AI Engine health
            if (!$this->aiService->isHealthy()) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI Engine tidak berjalan. Jalankan: cd python && uvicorn main:app --reload --port 8000',
                ], 503);
            }

            // Proses CV
            ProcessCVJob::dispatchSync($cv);

            // Ambil hasil terbaru
            $cv->load('matchingResult');
            $result = $cv->matchingResult;

            Log::info("Screening single CV #{$cvId} completed", [
                'score' => $result?->score,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'CV berhasil di-screen!',
                'data'    => [
                    'id'               => $result?->id,  // MatchingResult ID, not CV ID
                    'cv_id'            => $cv->id,
                    'score'            => $result?->score,
                    'status'           => $result?->status ?? 'Processed',
                    'rank'             => $result?->rank,
                    'has_result'       => true,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error("Screening single CV #{$cvId} failed: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Gagal screening CV: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Screening SEMUA CV untuk satu job (batch)
     */
    public function screenAll(Request $request, $jobId)
    {
        $job = UploadJob::findOrFail($jobId);
        $cvs = $job->cvs;

        if ($cvs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada CV untuk job ini.',
            ], 422);
        }

        if (!$this->aiService->isHealthy()) {
            return response()->json([
                'success' => false,
                'message' => 'AI Engine tidak berjalan. Jalankan: cd python && uvicorn main:app --reload --port 8000',
            ], 503);
        }

        $processed = 0;
        $failed = 0;
        $totalCvs = $cvs->count();

        foreach ($cvs as $index => $cv) {
            try {
                Log::info("Batch screening {$index}/{$totalCvs}: CV #{$cv->id}");
                ProcessCVJob::dispatchSync($cv);
                $processed++;

                // Jeda 3 detik antar CV biar tidak kena rate limit Gemini
                if ($index < $totalCvs - 1) {
                    sleep(3);
                }
            } catch (\Throwable $e) {
                Log::error("Batch screening failed for CV #{$cv->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        // Update rank
        $results = $job->matchingResults()->get();
        $rankScores = $results->pluck('score', 'id')->toArray();
        arsort($rankScores);
        $rank = 1;
        foreach (array_keys($rankScores) as $id) {
            $results->find($id)->update(['rank' => $rank++]);
        }

        return response()->json([
            'success' => true,
            'message' => "Screening selesai. {$processed} berhasil, {$failed} gagal.",
            'stats'   => [
                'processed' => $processed,
                'failed'    => $failed,
                'total'     => $totalCvs,
            ],
        ]);
    }
}