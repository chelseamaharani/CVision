@extends('layouts.dashboard')

@section('title', ($candidate['name'] ?? 'Candidate') . ' - CVision')

@section('content')

@php
$candidate = $candidate ?? [
    'name'        => 'Helena Gultom',
    'position'    => 'Python Developer',
    'cv_id'       => 'CV-2026-00123',
    'email'       => 'helena@gmail.com',
    'phone'       => '+62 821 4893 6402',
    'location'    => 'Batam, Indonesia',
    'score'       => 88,
    'rank'        => 2,
    'status'      => 'Highly Match',
    'percentile'  => 'Top 5% of candidates',
    'skills_matched' => ['Python', 'SQL', 'REST API'],
    'skills_total'   => 5,
    'skills_count'   => 4,
    'skill_gap'      => 'Django = 1 skill gap',
    'experience_years' => '2+ Years',
    'education'      => 'D3 Informatika',
    'similarity'     => 0.86,
    'cv_path'        => null,
    'experience' => [
        [
            'title'   => 'Python Developer',
            'company' => 'Tech Solutions',
            'period'  => 'Feb 2024 - Present',
            'duration'=> '2.3 Years',
            'points'  => [
                'Develop and maintained RESTful APIs using Python and Flask.',
                'Optimized SQL queries to improve database performance.',
                'Collaborated with a team of developers to implement backend services.',
            ],
        ],
    ],
    'recommendation' => 'Candidate is recommended because the CV shows strong alignment with required skills, sufficient experience, and relevant educational background. The candidate has solid Python expertise and proven ability to build scalable backend solutions.',
];
@endphp

{{-- ===================== HEADER: PROFILE ===================== --}}
<div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-6">

    <div class="flex items-center gap-5">
        {{-- Avatar --}}
        <div class="w-24 h-24 rounded-full bg-[#9AA0D8] flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.4c-3.5 0-9.8 1.7-9.8 5.1V22h19.6v-2.5c0-3.4-6.3-5.1-9.8-5.1z"/>
            </svg>
        </div>

        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $candidate['name'] }}</h1>
            <p class="text-gray-500 text-base mb-3">{{ $candidate['position'] }}</p>
            <span class="inline-block bg-[#DDE0F5] text-[#4B52B0] font-bold text-sm px-4 py-1.5 rounded-lg">
                ID : {{ $candidate['cv_id'] }}
            </span>
        </div>
    </div>

    {{-- Contact Info --}}
    <div class="bg-[#F0F2FF] rounded-xl px-5 py-4 flex flex-col gap-2 text-sm text-gray-700 min-w-[260px]">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $candidate['email'] }}
        </div>
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $candidate['phone'] }}
        </div>
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $candidate['location'] }}
        </div>
    </div>

</div>

{{-- ===================== SCORE + DETAILED BREAKDOWN ===================== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Score Card (2/3 width) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 flex flex-wrap items-center gap-8">

        {{-- Circle Score --}}
        <div class="relative w-28 h-28 flex-shrink-0">
            <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E8EAFF" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#38BDF8" stroke-width="3"
                    stroke-dasharray="{{ $candidate['score'] }}, 100" stroke-linecap="round"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-800">
                {{ $candidate['score'] }}%
            </span>
        </div>

        {{-- Matching Score --}}
        <div>
            <p class="font-bold text-gray-900 text-lg mb-2">Matching Score</p>
            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-600 text-sm font-semibold px-3 py-1 rounded-full mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                </svg>
                #{{ $candidate['rank'] }} Highly Match
            </span>
            <p class="text-gray-400 text-sm">{{ $candidate['percentile'] }}</p>
        </div>

        {{-- Rank --}}
        <div>
            <p class="font-bold text-gray-900 text-lg mb-2">Rank</p>
            <p class="text-2xl font-bold text-[#4B52B0] mb-1">#{{ $candidate['rank'] }}</p>
            <p class="text-gray-400 text-sm mb-2">Status</p>
            <span class="inline-block bg-green-100 text-green-600 text-sm font-semibold px-3 py-1 rounded-full">
                {{ $candidate['status'] }}
            </span>
        </div>

    </div>

    {{-- Detailed Breakdown (1/3 width) --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 text-lg mb-4">Detailed Breakdown</h3>

        <div class="flex flex-col divide-y divide-gray-100">

            {{-- Skill Match --}}
            <div class="py-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold text-gray-800 text-sm">Skill Match</span>
                    </div>
                    <span class="bg-[#DDE0F5] text-[#4B52B0] text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0">
                        {{ $candidate['skills_count'] }} of {{ $candidate['skills_total'] }} matched
                    </span>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-1">
                    @foreach($candidate['skills_matched'] as $skill)
                        <span class="bg-[#DDE0F5] text-[#4B52B0] text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $skill }}</span>
                    @endforeach
                </div>
                <p class="text-gray-400 text-xs">{{ $candidate['skill_gap'] }}</p>
            </div>

            {{-- Experience Fit --}}
            <div class="py-3">
                <div class="flex items-center gap-2 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold text-gray-800 text-sm">Experience Fit</span>
                </div>
                <p class="text-gray-500 text-xs mb-2">Meets minimum requirement</p>
                <span class="bg-green-100 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                    {{ $candidate['experience_years'] }}
                </span>
            </div>

            {{-- Education Fit --}}
            <div class="py-3">
                <div class="flex items-center gap-2 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0112 20.055a12.083 12.083 0 01-6.16-9.477L12 14z"/>
                    </svg>
                    <span class="font-semibold text-gray-800 text-sm">Education Fit</span>
                </div>
                <p class="text-gray-500 text-xs mb-2">Matches requirement</p>
                <span class="bg-[#DDE0F5] text-[#4B52B0] text-xs font-semibold px-2.5 py-1 rounded-full">
                    {{ $candidate['education'] }}
                </span>
            </div>

            {{-- Similarity Score --}}
            <div class="py-3">
                <div class="flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span class="font-semibold text-gray-800 text-sm">Similarity Score</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-800 text-sm">{{ $candidate['similarity'] }}</span>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#4B52B0] rounded-full" style="width: {{ $candidate['similarity'] * 100 }}%"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ===================== EXPERIENCE ===================== --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-900 text-lg">Experience</h3>
        <a href="#" class="text-[#4B52B0] text-sm font-semibold hover:underline flex items-center gap-1">
            View Full Story
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    @foreach($candidate['experience'] as $exp)
    <div class="flex gap-4 {{ !$loop->last ? 'mb-6' : '' }}">
        <div class="flex flex-col items-center flex-shrink-0">
            <div class="w-10 h-10 rounded-lg bg-[#E8EAFF] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            @if(!$loop->last)
                <div class="w-px flex-1 bg-gray-200 mt-2"></div>
            @endif
        </div>

        <div class="flex-1 pb-2">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-1">
                <h4 class="font-semibold text-gray-800 text-base">{{ $exp['title'] }}</h4>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-sm">{{ $exp['period'] }}</span>
                    <span class="bg-green-100 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $exp['duration'] }}</span>
                </div>
            </div>
            <p class="text-gray-400 text-sm mb-3">{{ $exp['company'] }}</p>
            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1.5">
                @foreach($exp['points'] as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endforeach
</div>

{{-- ===================== RECOMMENDATION ===================== --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18h6m-5 3h4m-7-6a7 7 0 1110 0c-.866 1.155-1.5 2.292-1.5 3.5h-7c0-1.208-.634-2.345-1.5-3.5z"/>
            </svg>
        </div>
        <h3 class="font-bold text-gray-900 text-lg">Recommendation</h3>
    </div>

    <div class="bg-[#F0F2FF] rounded-xl px-5 py-4">
        <p class="text-gray-700 text-sm leading-relaxed">
            {{ $candidate['recommendation'] }}
        </p>
    </div>
</div>

{{-- ===================== ACTION BUTTONS ===================== --}}
<div class="flex flex-col sm:flex-row items-center gap-3">

    <a href="{{ url()->previous() }}"
       class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#F0F2FF] hover:bg-[#E0E3FA] text-gray-700 font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Candidates
    </a>

    <a href="mailto:{{ $candidate['email'] }}"
       class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#F0F2FF] hover:bg-[#E0E3FA] text-gray-700 font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Contact via Email
    </a>

    @if($candidate['cv_path'])
        <a href="{{ asset('storage/' . $candidate['cv_path']) }}" target="_blank" download
           class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Resume
        </a>
    @else
        <span class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-gray-200 text-gray-400 font-semibold text-sm px-6 py-3 rounded-xl cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Resume
        </span>
    @endif

</div>

@endsection