<?php

use App\Jobs\ProcessCVJob;
use App\Models\Cv;
use App\Models\UploadJob;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

function makeUploadJob(User $hrd): UploadJob
{
    return UploadJob::create([
        'user_id' => $hrd->id,
        'title' => 'Backend Developer',
        'category' => 'IT',
        'description' => 'Deskripsi pekerjaan backend developer.',
        'required_skills' => 'PHP,Laravel,MySQL',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);
}

it('shows landing page with jobs list for guest', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    makeUploadJob($hrd);

    $response = $this->get(route('landing'));

    $response->assertOk();
    $response->assertViewHas('jobs');
    $response->assertViewHas('riwayatCv', []);
});

it('shows riwayat cv for authenticated pelamar', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar']);
    Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);

    $response = $this->actingAs($pelamar)->get(route('landing'));

    $response->assertOk();
    $response->assertViewHas('riwayatCv', function ($riwayatCv) {
        return $riwayatCv->count() === 1;
    });
});

it('uploads cv successfully and dispatches ProcessCVJob', function () {
    Storage::fake('public');
    Queue::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $file = UploadedFile::fake()->create('cv_budi.pdf', 500, 'application/pdf');

    $response = $this->actingAs($pelamar)->post('/upload-cv', [
        'upload_job_id' => $job->id,
        'cv_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('cvs', [
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_name' => 'cv_budi.pdf',
    ]);

    Queue::assertPushed(ProcessCVJob::class);
});

it('fails cv upload when upload_job_id is missing', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $file = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

    $response = $this->actingAs($pelamar)->post('/upload-cv', [
        'cv_file' => $file,
    ]);

    $response->assertSessionHasErrors('upload_job_id');
});

it('fails cv upload when file type is invalid', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $file = UploadedFile::fake()->create('cv.txt', 100, 'text/plain');

    $response = $this->actingAs($pelamar)->post('/upload-cv', [
        'upload_job_id' => $job->id,
        'cv_file' => $file,
    ]);

    $response->assertSessionHasErrors('cv_file');
});

it('fails cv upload when file exceeds 5MB', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $file = UploadedFile::fake()->create('cv_besar.pdf', 6000, 'application/pdf'); // 6MB > 5120KB

    $response = $this->actingAs($pelamar)->post('/upload-cv', [
        'upload_job_id' => $job->id,
        'cv_file' => $file,
    ]);

    $response->assertSessionHasErrors('cv_file');
});

it('guest cannot upload cv', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);

    $file = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

    $response = $this->post('/upload-cv', [
        'upload_job_id' => $job->id,
        'cv_file' => $file,
    ]);

    $response->assertRedirect(route('login'));
});

it('deletes own cv successfully', function () {
    Storage::fake('public');

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    Storage::disk('public')->put('cv_uploads/dummy.pdf', 'dummy content');

    $cv = Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);

    $response = $this->actingAs($pelamar)->delete("/upload-cv/{$cv->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('cvs', ['id' => $cv->id]);
    Storage::disk('public')->assertMissing('cv_uploads/dummy.pdf');
});

it('cannot delete another users cv', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeUploadJob($hrd);

    $ownerPelamar = User::factory()->create(['role' => 'pelamar']);
    $otherPelamar = User::factory()->create(['role' => 'pelamar']);

    $cv = Cv::create([
        'user_id' => $ownerPelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);

    $response = $this->actingAs($otherPelamar)->delete("/upload-cv/{$cv->id}");

    $response->assertNotFound();
});