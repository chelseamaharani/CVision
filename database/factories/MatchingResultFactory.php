<?php

namespace Database\Factories;

use App\Models\Cv;
use App\Models\MatchingResult;
use App\Models\UploadJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchingResultFactory extends Factory
{
    protected $model = MatchingResult::class;

    public function definition(): array
    {
        return [
            'upload_job_id' => UploadJob::factory(),
            'cv_id' => Cv::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'rank' => 0, // Will be calculated after save
            'status' => fake()->randomElement(['Processed', 'Failed', 'Pending']),
            'skills_matched' => fake()->randomElements(['PHP', 'Laravel', 'Python', 'JavaScript', 'SQL'], 3),
            'skills_total' => 5,
            'skills_count' => 3,
            'skill_gap' => fake()->randomElements(['Docker', 'AWS', 'React'], 2),
            'experience_years' => fake()->numberBetween(0, 10),
            'education_match' => fake()->randomElement(['S1', 'S2', 'D3', 'SMA/SMK']),
            'similarity_score' => fake()->randomFloat(4, 0, 1),
            'tfidf_score' => fake()->randomFloat(4, 0, 1),
            'sbert_score' => fake()->randomFloat(4, 0, 1),
            'hybrid_score' => fake()->randomFloat(4, 0, 1),
            'recommendation' => json_encode([
                'recommendations' => [
                    [
                        'job_title' => fake()->jobTitle(),
                        'confidence' => fake()->numberBetween(50, 95),
                        'reasoning' => fake()->sentence(),
                        'supporting_skills' => fake()->randomElements(['PHP', 'Laravel', 'JavaScript'], 3),
                    ]
                ]
            ]),
        ];
    }
}