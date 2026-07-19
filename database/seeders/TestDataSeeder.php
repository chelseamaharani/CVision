<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UploadJob;
use App\Models\Cv;
use App\Models\MatchingResult;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@cvision.test'],
            [
                'name' => 'Admin HRD',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Create applicant user if not exists
        $applicant = User::firstOrCreate(
            ['email' => 'applicant@cvision.test'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        // Create 3 sample jobs
        $jobs = UploadJob::factory()
            ->count(3)
            ->for($admin)
            ->create();

        // Create 5 sample CVs for each job
        foreach ($jobs as $job) {
            Cv::factory()
                ->count(5)
                ->for($applicant)
                ->create(['upload_job_id' => $job->id]);
        }

        // Create matching results for each CV
        foreach (Cv::all() as $cv) {
            MatchingResult::factory()
                ->for($cv)
                ->for($cv->uploadJob)
                ->create();
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Admin: admin@cvision.test / password');
        $this->command->info('Applicant: applicant@cvision.test / password');
    }
}