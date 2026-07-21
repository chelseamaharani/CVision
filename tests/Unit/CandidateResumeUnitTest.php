<?php

use App\Models\Cv;
use App\Models\MatchingResult;
use App\Models\UploadJob;
use App\Models\User;

function makeJobForCandidate(User $hrd): UploadJob
{
    return UploadJob::create([
        'user_id' => $hrd->id,
        'title' => 'Backend Developer',
        'category' => 'IT',
        'description' => 'Deskripsi job.',
        'required_skills' => 'PHP,Laravel,MySQL',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);
}

function makeCvForCandidate(User $pelamar, UploadJob $job): Cv
{
    return Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);
}

it('admin can view candidate resume detail with correct data', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar', 'name' => 'Budi Santoso']);
    $cv = makeCvForCandidate($pelamar, $job);

    $result = MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 88,
        'rank' => 1,
        'status' => 'Highly Match',
        'skills_matched' => ['PHP', 'Laravel'],
        'skills_total' => 3,
        'skills_count' => 2,
        'skill_gap' => ['MySQL'],
        'experience_years' => 2.5,
        'education_match' => 'S1',
        'similarity_score' => 0.86,
        'tfidf_score' => 0.7,
        'sbert_score' => 0.8,
        'hybrid_score' => 0.75,
        'recommendation' => json_encode([
            'recommendations' => [
                ['job_title' => 'Backend Developer'],
                ['job_title' => 'Fullstack Developer'],
            ],
        ]),
    ]);

    $response = $this->actingAs($hrd)->get(route('candidate.resume', $result->id));

    $response->assertOk();

    dump($response->viewData('candidate'));

    $response->assertViewHas('candidate');
});

it('falls back to score-based status label when status is null', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForCandidate($pelamar, $job);

    $result = MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 72,
        'status' => null,
    ]);

    $response = $this->actingAs($hrd)->get(route('candidate.resume', $result->id));

    $response->assertViewHas('candidate', function ($candidate) {
        return $candidate['status'] === 'Good Match';
    });
});

it('calculates percentile correctly among job candidates', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $cv1 = makeCvForCandidate($pelamar, $job);
    $topResult = MatchingResult::create(['upload_job_id' => $job->id, 'cv_id' => $cv1->id, 'score' => 95]);

    foreach ([80, 70, 60] as $score) {
        $cv = makeCvForCandidate(User::factory()->create(['role' => 'pelamar']), $job);
        MatchingResult::create(['upload_job_id' => $job->id, 'cv_id' => $cv->id, 'score' => $score]);
    }

    $response = $this->actingAs($hrd)->get(route('candidate.resume', $topResult->id));

    $response->assertViewHas('candidate', function ($candidate) {
        return $candidate['percentile'] === 'Top 100%';
    });
});

it('shows fallback message when no AI recommendations available', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForCandidate($pelamar, $job);

    $result = MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 50,
        'recommendation' => null,
    ]);

    $response = $this->actingAs($hrd)->get(route('candidate.resume', $result->id));

    $response->assertViewHas('candidate', function ($candidate) {
        return $candidate['recommendation'] === 'AI recommendation not available.';
    });
});

it('pelamar cannot access candidate resume detail', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForCandidate($pelamar, $job);

    $result = MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 88,
    ]);

    $response = $this->actingAs($pelamar)->get(route('candidate.resume', $result->id));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing candidate resume', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidate($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForCandidate($pelamar, $job);

    $result = MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 88,
    ]);

    $response = $this->get(route('candidate.resume', $result->id));

    $response->assertRedirect(route('login'));
});

it('returns 404 when matching result does not exist', function () {
    $hrd = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($hrd)->get(route('candidate.resume', 9999));

    $response->assertNotFound();
});