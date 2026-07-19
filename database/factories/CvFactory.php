<?php

namespace Database\Factories;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CvFactory extends Factory
{
    protected $model = Cv::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'upload_job_id' => \App\Models\UploadJob::factory(),
            'file_path' => 'cv_uploads/sample-cv-' . fake()->unique()->numberBetween(1, 1000) . '.pdf',
            'file_name' => 'CV_' . fake()->name() . '.pdf',
        ];
    }
}