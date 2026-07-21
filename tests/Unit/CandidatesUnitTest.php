<?php

use App\Models\Cv;
use App\Models\UploadJob;
use App\Models\User;

function makeJobForCandidates(User $hrd, string $title = 'Backend Developer'): UploadJob
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

it('admin can view candidates list with correct data', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForCandidates($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar', 'name' => 'Budi Santoso']);
    $cv = Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/budi.pdf',
        'file_name' => 'budi.pdf',
    ]);

    $response = $this->actingAs($hrd)->get(route('candidates.index'));

    $response->assertOk();
    $response->assertViewHas('candidatesList', function ($candidatesList) use ($cv) {
        $first = $candidatesList->first();

        return $candidatesList->count() === 1
            && $first['id'] === $cv->id
            && $first['name'] === 'Budi Santoso'
            && $first['position'] === 'Backend Developer'
            && $first['cv_path'] === 'cv_uploads/budi.pdf';
    });
});

it('shows empty list when no candidates have applied', function () {
    $hrd = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($hrd)->get(route('candidates.index'));

    $response->assertOk();
    $response->assertViewHas('candidatesList', function ($candidatesList) {
        return $candidatesList->count() === 0;
    });
});

it('lists candidates from multiple jobs ordered by latest', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $jobA = makeJobForCandidates($hrd, 'Backend Developer');
    $jobB = makeJobForCandidates($hrd, 'Frontend Developer');

    $pelamar1 = User::factory()->create(['role' => 'pelamar']);
    $pelamar2 = User::factory()->create(['role' => 'pelamar']);

    $olderCv = Cv::create([
        'user_id' => $pelamar1->id,
        'upload_job_id' => $jobA->id,
        'file_path' => 'cv_uploads/a.pdf',
        'file_name' => 'a.pdf',
    ]);
    $olderCv->forceFill(['created_at' => now()->subMinutes(10)])->save();

    $latestCv = Cv::create([
        'user_id' => $pelamar2->id,
        'upload_job_id' => $jobB->id,
        'file_path' => 'cv_uploads/b.pdf',
        'file_name' => 'b.pdf',
    ]);
    $latestCv->forceFill(['created_at' => now()])->save();

    $response = $this->actingAs($hrd)->get(route('candidates.index'));

    $response->assertViewHas('candidatesList', function ($candidatesList) use ($latestCv) {
        return $candidatesList->count() === 2
            && $candidatesList->first()['id'] === $latestCv->id;
    });
});

it('pelamar cannot access candidates list', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($pelamar)->get(route('candidates.index'));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing candidates list', function () {
    $response = $this->get(route('candidates.index'));

    $response->assertRedirect(route('login'));
});