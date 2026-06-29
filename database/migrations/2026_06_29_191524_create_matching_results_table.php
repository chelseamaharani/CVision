<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matching_results', function (Blueprint $table) {
            $table->id();

            // Relasi ke job dan CV yang di-screening
            $table->foreignId('upload_job_id')->constrained('upload_jobs')->onDelete('cascade');
            $table->foreignId('cv_id')->constrained('cvs')->onDelete('cascade');

            // Hasil dari AI (nanti diisi oleh model AI temanmu)
            $table->integer('score')->default(0);              // contoh: 88 (artinya 88%)
            $table->integer('rank')->nullable();                 // urutan ranking, contoh: 1, 2, 3
            $table->string('status')->nullable();                 // contoh: "Highly Match", "Good Match"

            // Detailed breakdown (disimpan sebagai JSON biar fleksibel)
            $table->json('skills_matched')->nullable();           // contoh: ["Python", "SQL", "REST API"]
            $table->integer('skills_total')->nullable();           // total skill yang dibutuhkan job
            $table->integer('skills_count')->nullable();           // jumlah skill yang cocok
            $table->string('skill_gap')->nullable();               // contoh: "Django = 1 skill gap"

            $table->string('experience_years')->nullable();       // contoh: "2+ Years"
            $table->string('education_match')->nullable();         // contoh: "D3 Informatika"
            $table->decimal('similarity_score', 4, 2)->nullable(); // contoh: 0.86

            $table->text('recommendation')->nullable();             // teks rekomendasi dari AI

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matching_results');
    }
};