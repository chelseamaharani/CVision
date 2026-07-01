<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matching_results', function (Blueprint $table) {
            // Change skill_gap from string to json for array storage
            $table->json('skill_gap')->nullable()->change();

            // Change experience_years from string to float
            $table->float('experience_years')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_results', function (Blueprint $table) {
            $table->string('skill_gap')->nullable()->change();
            $table->string('experience_years')->nullable()->change();
        });
    }
};