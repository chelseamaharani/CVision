<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'upload_job_id',
    'file_path',
    'file_name',
])]
class Cv extends Model
{
    use HasFactory;

    /**
     * Pelamar yang mengupload CV ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Posisi/job yang dilamar
     */
    public function uploadJob(): BelongsTo
    {
        return $this->belongsTo(UploadJob::class);
    }

    /**
     * Hasil screening AI untuk CV ini (1 CV = 1 hasil matching per job)
     */
    public function matchingResult(): HasOne
    {
        return $this->hasOne(MatchingResult::class);
    }
}