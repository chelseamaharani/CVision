<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'upload_job_id',
    'cv_id',
    'score',
    'rank',
    'status',
    'skills_matched',
    'skills_total',
    'skills_count',
    'skill_gap',
    'experience_years',
    'education_match',
    'similarity_score',
    'tfidf_score',
    'sbert_score',
    'hybrid_score',
    'recommendation',
])]
class MatchingResult extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'skills_matched'   => 'array',
            'skill_gap'        => 'array',
            'similarity_score' => 'float',
            'score'            => 'float',
            'tfidf_score'      => 'float',
            'sbert_score'      => 'float',
            'hybrid_score'     => 'float',
            'experience_years' => 'float',
        ];
    }

    /**
     * Job yang di-screening
     */
    public function uploadJob(): BelongsTo
    {
        return $this->belongsTo(UploadJob::class);
    }

    /**
     * CV yang di-screening
     */
    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
