<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();

            // Pelamar yang upload
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Posisi yang dilamar (relasi ke upload_jobs)
            $table->foreignId('upload_job_id')->constrained('upload_jobs')->onDelete('cascade');

            $table->string('file_path');   // path file di storage, contoh: cv_uploads/xxxx.pdf
            $table->string('file_name');   // nama asli file waktu diupload, contoh: CV_Salsabila.pdf

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};