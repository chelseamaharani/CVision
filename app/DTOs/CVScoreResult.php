<?php

namespace App\DTOs;

/**
 * Data Transfer Object for CV Analysis Results
 * 
 * Provides a typed, immutable representation of AI analysis output.
 */
class CVScoreResult
{
    public function __construct(
        public readonly float $tfidfScore,
        public readonly float $sbertScore,
        public readonly float $hybridScore,
        public readonly float $matchPercentage,
        public readonly array $recommendation,
        public readonly ?array $skillGap = null,
        public readonly float $experienceYears = 0.0,
        public readonly string $educationLevel = 'Unknown',
        public readonly ?float $minExperienceYears = null,
        public readonly ?string $requiredEducation = null,
    ) {}

    /**
     * Create from the FastAPI JSON response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tfidfScore: $data['tfidf_score'] ?? 0.0,
            sbertScore: $data['sbert_score'] ?? 0.0,
            hybridScore: $data['hybrid_score'] ?? 0.0,
            matchPercentage: $data['match_percentage'] ?? 0.0,
            recommendation: $data['recommendation'] ?? [],
            skillGap: $data['skill_gap'] ?? null,
            experienceYears: $data['experience_years'] ?? 0.0,
            educationLevel: $data['education_level'] ?? 'Unknown',
            minExperienceYears: $data['min_experience'] ?? null,
            requiredEducation: $data['required_education'] ?? null,
        );
    }

    /**
     * Convert to array for database storage.
     */
    public function toArray(): array
    {
        // Clean recommendation data to ensure valid UTF-8 before JSON encoding
        $cleanedRecommendation = $this->cleanUtf8($this->recommendation);

        return [
            'similarity_score' => $this->matchPercentage,
            'tfidf_score'      => $this->tfidfScore,
            'sbert_score'      => $this->sbertScore,
            'hybrid_score'     => $this->hybridScore,
            'score'            => $this->matchPercentage,
            'recommendation'   => json_encode($cleanedRecommendation, JSON_INVALID_UTF8_SUBSTITUTE),
        ];
    }

    /**
     * Recursively clean array/string to ensure valid UTF-8 encoding.
     * Removes or replaces invalid UTF-8 characters that cause json_encode() to fail.
     */
    private function cleanUtf8(mixed $data): mixed
    {
        if (is_string($data)) {
            // Remove invalid UTF-8 characters by re-encoding with auto-detection
            $cleaned = mb_convert_encoding($data, 'UTF-8', 'auto');
            // Also strip any remaining invalid byte sequences
            $cleaned = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $cleaned);
            return $cleaned;
        }

        if (is_array($data)) {
            return array_map([$this, 'cleanUtf8'], $data);
        }

        return $data;
    }
}