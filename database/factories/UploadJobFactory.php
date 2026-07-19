<?php

namespace Database\Factories;

use App\Models\UploadJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UploadJobFactory extends Factory
{
    protected $model = UploadJob::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->jobTitle(),
            'category' => fake()->randomElement(['Technology', 'Finance', 'Marketing', 'Design', 'Healthcare']),
            'description' => fake()->paragraphs(3, true),
            'required_skills' => implode(',', fake()->randomElements([
                'PHP', 'Laravel', 'Python', 'JavaScript', 'React', 'Vue',
                'SQL', 'MySQL', 'PostgreSQL', 'Git', 'Docker', 'AWS',
                'Machine Learning', 'Data Analysis', 'Project Management'
            ], 5)),
            'min_experience' => fake()->numberBetween(0, 5),
            'education_requirement' => fake()->randomElement(['SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3']),
        ];
    }
}