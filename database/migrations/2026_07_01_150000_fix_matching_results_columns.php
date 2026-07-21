<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ===== 1. Bersihkan skill_gap =====
        DB::table('matching_results')
            ->where(function ($query) {
                $query->whereNull('skill_gap')
                      ->orWhere('skill_gap', '');
            })
            ->update(['skill_gap' => '[]']);

        $invalidSkillGap = DB::table('matching_results')
            ->whereNotNull('skill_gap')
            ->where('skill_gap', '!=', '')
            ->get(['id', 'skill_gap']);

        foreach ($invalidSkillGap as $row) {
            json_decode($row->skill_gap);
            if (json_last_error() !== JSON_ERROR_NONE) {
                DB::table('matching_results')
                    ->where('id', $row->id)
                    ->update(['skill_gap' => json_encode([$row->skill_gap])]);
            }
        }

        // ===== 2. Bersihkan experience_years =====
        $rows = DB::table('matching_results')->get(['id', 'experience_years']);

        foreach ($rows as $row) {
            $value = $row->experience_years;

            // Ambil angka (termasuk desimal) dari string, kalau ada
            if (is_numeric($value)) {
                continue; // sudah valid, skip
            }

            if (preg_match('/[\d.]+/', (string) $value, $matches)) {
                $cleanValue = (float) $matches[0];
            } else {
                $cleanValue = null; // kalau gak ada angka sama sekali
            }

            DB::table('matching_results')
                ->where('id', $row->id)
                ->update(['experience_years' => $cleanValue]);
        }

        // ===== 3. Ubah tipe kolom =====
        Schema::table('matching_results', function (Blueprint $table) {
            $table->json('skill_gap')->nullable()->change();
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