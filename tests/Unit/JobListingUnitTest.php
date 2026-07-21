<?php

use App\Models\UploadJob;
use App\Models\User;

it('admin can view job listing page with applicant counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $job = UploadJob::create([
        'user_id' => $admin->id,
        'title' => 'Backend Developer',
        'category' => 'IT',
        'description' => 'Deskripsi job.',
        'required_skills' => 'PHP,Laravel',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);

    $response = $this->actingAs($admin)->get(route('job_listing.index'));

    $response->assertOk();
    $response->assertViewHas('jobsList', function ($jobsList) use ($job) {
        return $jobsList->count() === 1
            && $jobsList->first()['id'] === $job->id
            && $jobsList->first()['applicants'] === 0;
    });
});

it('pelamar cannot access job listing page', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($pelamar)->get(route('job_listing.index'));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing job listing', function () {
    $response = $this->get(route('job_listing.index'));

    $response->assertRedirect(route('login'));
});

it('admin can view create job form', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('job_listing.create'));

    $response->assertOk();
});

it('admin can create a new job listing', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('job_listing.store'), [
        'job_title' => 'Backend Developer',
        'job_category' => 'IT',
        'job_description' => 'Deskripsi lengkap job backend.',
        'required_skills' => 'PHP,Laravel,MySQL',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('upload_jobs', [
        'user_id' => $admin->id,
        'title' => 'Backend Developer',
        'category' => 'IT',
    ]);
});

it('uses custom category when job_category is Others', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('job_listing.store'), [
        'job_title' => 'Data Scientist',
        'job_category' => 'Others',
        'job_category_other' => 'Data & Analytics',
        'job_description' => 'Deskripsi job data scientist.',
        'required_skills' => 'Python,SQL',
        'min_experience' => '2-3',
        'education_requirement' => 'S1',
    ]);

    $this->assertDatabaseHas('upload_jobs', [
        'title' => 'Data Scientist',
        'category' => 'Data & Analytics',
    ]);
});

it('fails to create job when job_category_other is missing while category is Others', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('job_listing.store'), [
        'job_title' => 'Data Scientist',
        'job_category' => 'Others',
        'job_description' => 'Deskripsi job.',
        'required_skills' => 'Python,SQL',
        'min_experience' => '2-3',
        'education_requirement' => 'S1',
    ]);

    $response->assertSessionHasErrors('job_category_other');
});

it('fails to create job when required fields are missing', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('job_listing.store'), [
        'job_title' => 'Backend Developer',
    ]);

    $response->assertSessionHasErrors([
        'job_category',
        'job_description',
        'required_skills',
        'min_experience',
        'education_requirement',
    ]);
});

it('pelamar cannot create job listing', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($pelamar)->post(route('job_listing.store'), [
        'job_title' => 'Backend Developer',
        'job_category' => 'IT',
        'job_description' => 'Deskripsi job.',
        'required_skills' => 'PHP',
        'min_experience' => '1-2',
        'education_requirement' => 'S1',
    ]);

    $response->assertForbidden();
});