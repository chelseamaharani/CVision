<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Tampilkan hasil matching kandidat
     */
    public function index(Request $request)
    {
        // Dummy data sementara biar view tidak error
        $job = (object) [
            'title' => 'Backend Developer'
        ];

        $stats = [
            'matches'    => 5,
            'candidates' => 20,
            'accuracy'   => '82%',
            'date'       => now()->format('M d, Y'),
        ];

        $candidates = [
            [
                'rank' => 1,
                'initials' => 'BS',
                'name' => 'Budi Santoso',
                'role' => 'Software Engineer',
                'score' => 92,
                'top' => true,
                'skills' => ['Python', 'SQL', 'REST API']
            ],
            [
                'rank' => 2,
                'initials' => 'AN',
                'name' => 'Andi Nugraha',
                'role' => 'Backend Developer',
                'score' => 88,
                'top' => false,
                'skills' => ['Laravel', 'MySQL', 'API']
            ],
        ];

        return view('pages.matching_results', compact('job', 'stats', 'candidates'));
    }
}