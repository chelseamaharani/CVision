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

            $table->float('tfidf_score')
                ->nullable()
                ->after('similarity_score');

            $table->float('sbert_score')
                ->nullable()
                ->after('tfidf_score');

            $table->float('hybrid_score')
                ->nullable()
                ->after('sbert_score');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_results', function (Blueprint $table) {

            $table->dropColumn([
                'tfidf_score',
                'sbert_score',
                'hybrid_score',
            ]);

        });
    }
};