<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCVJob;
use App\Models\Cv;
use App\Models\UploadJob;
use App\Services\AIService;
use Illuminate\Console\Command;

/**
 * Process all unprocessed CVs for a specific job, or all jobs.
 *
 * Usage:
 *   php artisan cv:process --job=1     Process all unprocessed CVs for job ID 1
 *   php artisan cv:process              Process all unprocessed CVs for all jobs
 */
class ProcessCVBatch extends Command
{
    protected $signature = 'cv:process
        {--job= : Process CVs for a specific job ID}
        {--force : Re-process already processed CVs}
    ';

    protected $description = 'Batch process all unprocessed CVs through the AI engine';

    /**
     * Execute the console command.
     */
    public function handle(AIService $aiService): int
    {
        // Check if AI engine is healthy
        $this->components->task('Checking AI Engine health', function () use ($aiService) {
            return $aiService->isHealthy();
        });

        if (!$aiService->isHealthy()) {
            $this->components->error('AI Engine is not running. Start it with: uvicorn main:app --port 8000');
            return Command::FAILURE;
        }

        $this->components->info('AI Engine is healthy.');

        // Build query
        $query = Cv::query()->with('uploadJob');

        if ($jobId = $this->option('job')) {
            $query->where('upload_job_id', $jobId);

            $job = UploadJob::find($jobId);
            if (!$job) {
                $this->components->error("Job #{$jobId} not found.");
                return Command::FAILURE;
            }
            $this->components->info("Processing CVs for job: {$job->title} (#{$job->id})");
        }

        // Unless --force, only process CVs without matching results
        if (!$this->option('force')) {
            $query->whereDoesntHave('matchingResult');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->components->info('No CVs to process.');
            return Command::SUCCESS;
        }

        $this->components->info("Found {$total} CV(s) to process.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        $failed = 0;

        $query->chunk(10, function ($cvs) use ($bar, &$processed, &$failed) {
            foreach ($cvs as $cv) {
                try {
                    ProcessCVJob::dispatchSync($cv);
                    $processed++;
                } catch (\Throwable $e) {
                    $this->components->error("Failed: CV #{$cv->id} - {$e->getMessage()}");
                    $failed++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->components->twoColumnDetail('Processed', (string) $processed);
        $this->components->twoColumnDetail('Failed', (string) $failed);

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}