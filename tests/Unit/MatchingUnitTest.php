<?php

use App\Models\Cv;
use App\Models\MatchingResult;
use App\Models\UploadJob;
use App\Models\User;

function makeJobForMatching(User $hrd, string $title = 'Backend Developer'): UploadJob
{
    return UploadJob::create([
        'user_id' => $hrd->id,
        'title' => $title,
        'category' => 'IT',
        'description' => 'Deskripsi job.',
        'required_skills' => 'PHP,Laravel',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);
}

function makeCvForMatching(User $pelamar, UploadJob $job): Cv
{
    return Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);
}

it('admin can view matching history showing only jobs with matching results', function () {
    $hrd = User::factory()->create(['role' => 'admin']);

    $jobWithResult = makeJobForMatching($hrd, 'Backend Developer');
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForMatching($pelamar, $jobWithResult);
    MatchingResult::create([
        'upload_job_id' => $jobWithResult->id,
        'cv_id' => $cv->id,
        'score' => 80,
        'status' => 'Processed',
    ]);

    // Job tanpa matching result, harusnya gak muncul
    makeJobForMatching($hrd, 'Frontend Developer');

    $response = $this->actingAs($hrd)->get(route('matching.index'));

    $response->assertOk();
    $response->assertViewHas('historyList', function ($historyList) use ($jobWithResult) {
        return $historyList->count() === 1
            && $historyList->first()['id'] === $jobWithResult->id;
    });
});

it('admin can view matching results for a job with correct candidate data', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForMatching($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar', 'name' => 'Budi Santoso']);
    $cv = makeCvForMatching($pelamar, $job);

    MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv->id,
        'score' => 90,
        'rank' => 1,
        'status' => 'Processed',
        'skills_matched' => ['PHP'],
        'skill_gap' => ['MySQL'],
        'recommendation' => json_encode([
            'recommendations' => [['job_title' => 'Backend Developer']],
        ]),
    ]);

    $response = $this->actingAs($hrd)->get(route('matching.results', ['job_id' => $job->id]));

    $response->assertOk();
    $response->assertViewHas('candidates', function ($candidates) {
        $first = $candidates->first();

        return $candidates->count() === 1
            && $first['name'] === 'Budi Santoso'
            && $first['initials'] === 'BS'
            && (float) $first['score'] === 90.0
            && $first['top'] === true
            && $first['recommended_job'] === 'Backend Developer';
    });
});

it('filters matching results by min_score', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForMatching($hrd);

    foreach ([90, 50] as $score) {
        $pelamar = User::factory()->create(['role' => 'pelamar']);
        $cv = makeCvForMatching($pelamar, $job);
        MatchingResult::create([
            'upload_job_id' => $job->id,
            'cv_id' => $cv->id,
            'score' => $score,
            'status' => 'Processed',
        ]);
    }

    $response = $this->actingAs($hrd)->get(route('matching.results', [
        'job_id' => $job->id,
        'min_score' => 80,
    ]));

    $response->assertViewHas('candidates', function ($candidates) {
        return $candidates->count() === 1 && (float) $candidates->first()['score'] === 90.0;
    });
});

it('filters matching results by status', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForMatching($hrd);

    $pelamar1 = User::factory()->create(['role' => 'pelamar']);
    $cv1 = makeCvForMatching($pelamar1, $job);
    MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv1->id,
        'score' => 90,
        'status' => 'Processed',
    ]);

    $pelamar2 = User::factory()->create(['role' => 'pelamar']);
    $cv2 = makeCvForMatching($pelamar2, $job);
    MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv2->id,
        'score' => 0,
        'status' => 'Failed',
    ]);

    $response = $this->actingAs($hrd)->get(route('matching.results', [
        'job_id' => $job->id,
        'status' => 'Failed',
    ]));

    $response->assertViewHas('candidates', function ($candidates) {
        return $candidates->count() === 1 && $candidates->first()['status'] === 'Failed';
    });
});

it('calculates stats correctly for matching results', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForMatching($hrd);

    foreach ([80, 60] as $score) {
        $pelamar = User::factory()->create(['role' => 'pelamar']);
        $cv = makeCvForMatching($pelamar, $job);
        MatchingResult::create([
            'upload_job_id' => $job->id,
            'cv_id' => $cv->id,
            'score' => $score,
            'status' => 'Processed',
        ]);
    }

    $response = $this->actingAs($hrd)->get(route('matching.results', ['job_id' => $job->id]));

    $response->assertViewHas('stats', function ($stats) {
        return $stats['matches'] === 2
            && $stats['candidates'] === 2
            && $stats['accuracy'] === '70%'
            && $stats['top_score'] === '80%';
    });
});

it('score distribution only counts processed results', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForMatching($hrd);

    $pelamar1 = User::factory()->create(['role' => 'pelamar']);
    $cv1 = makeCvForMatching($pelamar1, $job);
    MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv1->id,
        'score' => 85,
        'status' => 'Processed',
    ]);

    $pelamar2 = User::factory()->create(['role' => 'pelamar']);
    $cv2 = makeCvForMatching($pelamar2, $job);
    MatchingResult::create([
        'upload_job_id' => $job->id,
        'cv_id' => $cv2->id,
        'score' => 0,
        'status' => 'Failed', // harusnya gak ke-count di distribution
    ]);

    $response = $this->actingAs($hrd)->get(route('matching.results', ['job_id' => $job->id]));

    $response->assertViewHas('distribution', function ($distribution) {
        return $distribution['81-100'] === 1
            && array_sum($distribution) === 1; // cuma yang Processed doang
    });
});

it('returns 404 when job does not exist', function () {
    $hrd = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($hrd)->get(route('matching.results', ['job_id' => 9999]));

    $response->assertNotFound();
});

it('pelamar cannot access matching history', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($pelamar)->get(route('matching.index'));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing matching history', function () {
    $response = $this->get(route('matching.index'));

    $response->assertRedirect(route('login'));
});