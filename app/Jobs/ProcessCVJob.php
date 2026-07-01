<?php

namespace App\Jobs;

use App\Models\Cv;
use App\Services\CVScoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queue job for asynchronous CV processing.
 *
 * Dispatched after a CV is uploaded. Runs the AI analysis pipeline
 * in the background so the user gets an immediate response.
 */
class ProcessCVJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Cv $cv,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CVScoreService $scoreService): void
    {
        Log::info("Processing CV #{$this->cv->id} via queue job", [
            'job_id' => $this->cv->upload_job_id,
            'file'   => $this->cv->file_name,
        ]);

        try {
            $result = $scoreService->analyze($this->cv);

            Log::info("CV #{$this->cv->id} processed successfully", [
                'score'    => $result->matchPercentage,
                'job_id'   => $this->cv->upload_job_id,
            ]);

            // TODO: Send notification to HRD that a new CV has been processed
            // event(new CVProcessed($this->cv, $result));

        } catch (\Throwable $e) {
            Log::error("Failed to process CV #{$this->cv->id}", [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Mark the CV as failed
            $this->cv->matchingResult()?->update(['status' => 'Failed']);

            // Re-throw so the queue system handles retries
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $e = null): void
    {
        Log::error("CV #{$this->cv->id} processing failed after all retries", [
            'error' => $e?->getMessage(),
        ]);

        // Mark as failed permanently
        if ($this->cv->matchingResult) {
            $this->cv->matchingResult->update(['status' => 'Failed']);
        }
    }
}