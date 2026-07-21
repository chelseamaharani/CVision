<?php

use App\Jobs\ProcessCVJob;
use App\Models\Cv;
use App\Models\UploadJob;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Support\Facades\Bus;

function makeJobForScreening(User $hrd): UploadJob
{
    return UploadJob::create([
        'user_id' => $hrd->id,
        'title' => 'Backend Developer',
        'category' => 'IT',
        'description' => 'Deskripsi job.',
        'required_skills' => 'PHP,Laravel',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);
}

function makeCvForScreening(User $pelamar, UploadJob $job): Cv
{
    return Cv::create([
        'user_id' => $pelamar->id,
        'upload_job_id' => $job->id,
        'file_path' => 'cv_uploads/dummy.pdf',
        'file_name' => 'dummy.pdf',
    ]);
}

it('admin can view screening cv list for a job', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);

    $pelamar = User::factory()->create(['role' => 'pelamar', 'name' => 'Budi Santoso']);
    makeCvForScreening($pelamar, $job);

    $response = $this->actingAs($hrd)->get(route('screening.index', $job->id));

    $response->assertOk();
    $response->assertViewHas('cvs', function ($cvs) {
        $first = $cvs->first();

        return $cvs->count() === 1
            && $first['name'] === 'Budi Santoso'
            && $first['status'] === 'Pending'
            && $first['has_result'] === false;
    });
});

it('returns 404 when screening a job that does not exist', function () {
    $hrd = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($hrd)->get(route('screening.index', 9999));

    $response->assertNotFound();
});

it('screens a single cv successfully when AI engine is healthy', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForScreening($pelamar, $job);

    $this->mock(AIService::class, function ($mock) {
        $mock->shouldReceive('isHealthy')->once()->andReturn(true);
    });

    $response = $this->actingAs($hrd)->post(route('screening.screen', $cv->id));

    $response->assertOk();
    $response->assertJson(['success' => true]);

    Bus::assertDispatchedSync(ProcessCVJob::class, function ($job) use ($cv) {
        return $job->cv->id === $cv->id;
    });
});

it('fails to screen single cv when AI engine is not healthy', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $cv = makeCvForScreening($pelamar, $job);

    $this->mock(AIService::class, function ($mock) {
        $mock->shouldReceive('isHealthy')->once()->andReturn(false);
    });

    $response = $this->actingAs($hrd)->post(route('screening.screen', $cv->id));

    $response->assertStatus(503);
    $response->assertJson(['success' => false]);

    Bus::assertNotDispatched(ProcessCVJob::class);
});

it('returns error json when screening a cv that does not exist', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($hrd)->post(route('screening.screen', 9999));

    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

it('screens all cvs for a job when AI engine is healthy', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);

    $pelamar1 = User::factory()->create(['role' => 'pelamar']);
    $pelamar2 = User::factory()->create(['role' => 'pelamar']);
    makeCvForScreening($pelamar1, $job);
    makeCvForScreening($pelamar2, $job);

    $this->mock(AIService::class, function ($mock) {
        $mock->shouldReceive('isHealthy')->once()->andReturn(true);
    });

    $response = $this->actingAs($hrd)->post(route('screening.screenAll', $job->id));

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'stats' => ['processed' => 2, 'failed' => 0, 'total' => 2],
    ]);

    Bus::assertDispatchedSync(ProcessCVJob::class, 2);
});

it('fails to screen all when job has no cvs', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);

    $response = $this->actingAs($hrd)->post(route('screening.screenAll', $job->id));

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);

    Bus::assertNotDispatched(ProcessCVJob::class);
});

it('fails to screen all when AI engine is not healthy', function () {
    Bus::fake();

    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    makeCvForScreening($pelamar, $job);

    $this->mock(AIService::class, function ($mock) {
        $mock->shouldReceive('isHealthy')->once()->andReturn(false);
    });

    $response = $this->actingAs($hrd)->post(route('screening.screenAll', $job->id));

    $response->assertStatus(503);
    Bus::assertNotDispatched(ProcessCVJob::class);
});

it('pelamar cannot access screening pages', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);
    $job = makeJobForScreening($pelamar); // pura-pura user_id, gak masalah buat test ini

    $response = $this->actingAs($pelamar)->get(route('screening.index', $job->id));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing screening', function () {
    $hrd = User::factory()->create(['role' => 'admin']);
    $job = makeJobForScreening($hrd);

    $response = $this->get(route('screening.index', $job->id));

    $response->assertRedirect(route('login'));
});