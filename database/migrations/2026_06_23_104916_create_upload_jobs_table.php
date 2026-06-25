<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_jobs', function (Blueprint $table) {
            $table->id();

            // Siapa yang posting (HRD/admin)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('title');                 // job_title
            $table->string('category');               // job_category (atau job_category_other kalau "Others")
            $table->text('description');               // job_description
            $table->text('required_skills');           // disimpan sebagai string dipisah koma: "Python,SQL,REST API"
            $table->string('min_experience');           // "0-1", "1-2", "2-3", "3-5", "5+"
            $table->string('education_requirement');    // "SMA", "D3", "D4", "S1", "S2"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_jobs');
    }
};