<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'title',
    'category',
    'description',
    'required_skills',
    'min_experience',
    'education_requirement',
])]
class UploadJob extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit
     * (karena nama model UploadJob secara default akan dicari di tabel 'upload_jobs',
     * jadi sebenarnya baris ini opsional, tapi ditulis untuk kejelasan)
     */
    protected $table = 'upload_jobs';

    /**
     * HRD/admin yang membuat job ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * CV yang melamar ke job ini
     */
    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class, 'upload_job_id');
    }

    /**
     * Hasil screening AI untuk job ini
     */
    public function matchingResults(): HasMany
    {
        return $this->hasMany(MatchingResult::class, 'upload_job_id')->orderByDesc('score');
    }

    /**
     * Helper: ambil required_skills sebagai array
     * Contoh: "Python,SQL,REST API" -> ['Python', 'SQL', 'REST API']
     */
    public function getSkillsArrayAttribute(): array
    {
        return array_filter(array_map('trim', explode(',', $this->required_skills)));
    }
}