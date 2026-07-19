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

{{-- ===================== SCORE CARD ===================== --}}
<div class="bg-white rounded-2xl shadow-sm p-6 flex flex-wrap items-center gap-8 mb-6">

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

{{-- ===================== RESUME ===================== --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#E8EAFF] flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg">Resume</h3>
            <span class="text-gray-400 text-xs">Parsed from CV</span>
        </div>
    </div>

    @if(!empty($candidate['structured_resume']))
        @php $resume = $candidate['structured_resume']; @endphp
        
        {{-- Name & Contact --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $resume['name'] ?? $candidate['name'] }}</h2>
            @if(!empty($resume['email']) || !empty($resume['phone']) || !empty($resume['address']))
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                @if(!empty($resume['email']))
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $resume['email'] }}
                </span>
                @endif
                @if(!empty($resume['phone']))
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $resume['phone'] }}
                </span>
                @endif
                @if(!empty($resume['address']))
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $resume['address'] }}
                </span>
                @endif
            </div>
            @endif
        </div>

        {{-- Professional Summary --}}
        @if(!empty($resume['summary']))
        <div class="mb-5">
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-2">Professional Summary</h4>
            <p class="text-gray-600 text-sm leading-relaxed">{{ $resume['summary'] }}</p>
        </div>
        @endif

        {{-- Experience --}}
        @if(!empty($resume['experience']))
        <div class="mb-5">
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-3">Work Experience</h4>
            <div class="space-y-4">
                @foreach($resume['experience'] as $exp)
                <div class="border-l-2 border-[#4B52B0] pl-4">
                    <div class="flex items-start justify-between flex-wrap gap-1">
                        <h5 class="font-semibold text-gray-800 text-sm">{{ $exp['title'] ?? '' }}</h5>
                        @if(!empty($exp['period']))
                        <span class="text-gray-400 text-xs">{{ $exp['period'] }}</span>
                        @endif
                    </div>
                    @if(!empty($exp['company']))
                    <p class="text-gray-500 text-xs mb-1">{{ $exp['company'] }}</p>
                    @endif
                    @if(!empty($exp['description']))
                    <ul class="list-disc list-inside text-gray-600 text-xs space-y-1 mt-1">
                        @foreach((array)$exp['description'] as $desc)
                        <li>{{ $desc }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Education --}}
        @if(!empty($resume['education']))
        <div class="mb-5">
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-3">Education</h4>
            <div class="space-y-3">
                @foreach($resume['education'] as $edu)
                <div class="flex items-start justify-between flex-wrap gap-1">
                    <div>
                        @if(!empty($edu['degree']))
                        <p class="font-semibold text-gray-800 text-sm">{{ $edu['degree'] }}</p>
                        @endif
                        @if(!empty($edu['institution']))
                        <p class="text-gray-500 text-xs">{{ $edu['institution'] }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if(!empty($edu['year']))
                        <span class="text-gray-400 text-xs">{{ $edu['year'] }}</span>
                        @endif
                        @if(!empty($edu['gpa']))
                        <br><span class="text-gray-400 text-xs">GPA: {{ $edu['gpa'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Skills --}}
        @if(!empty($resume['skills']))
        <div class="mb-5">
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-3">Skills</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($resume['skills'] as $skill)
                <span class="inline-block bg-[#E8EAFF] text-[#4B52B0] text-xs font-medium px-3 py-1 rounded-full">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Certifications --}}
        @if(!empty($resume['certifications']))
        <div class="mb-5">
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-3">Certifications</h4>
            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                @foreach($resume['certifications'] as $cert)
                <li>{{ $cert }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Languages --}}
        @if(!empty($resume['languages']))
        <div>
            <h4 class="font-semibold text-gray-800 text-sm uppercase tracking-wide mb-3">Languages</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($resume['languages'] as $lang)
                <span class="inline-block bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full">{{ $lang }}</span>
                @endforeach
            </div>
        </div>
        @endif
    @elseif(!empty($candidate['cv_text']))
        {{-- Fallback: Show raw CV text if structured resume not available --}}
        <div class="bg-gray-50 rounded-xl p-5 max-h-96 overflow-y-auto">
            <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans leading-relaxed">{{ $candidate['cv_text'] }}</pre>
        </div>
    @else
        <div class="text-center py-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">CV text has not been extracted yet.</p>
        </div>
    @endif
</div>

{{-- AI Job Recommendations removed for HRD view --}}

{{-- ===================== ACTION BUTTONS ===================== --}}
<div class="flex flex-col sm:flex-row items-center gap-3">

    <a href="{{ url()->previous() }}"
       class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#F0F2FF] hover:bg-[#E0E3FA] text-gray-700 font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Candidates
    </a>

    @php
        // Pastikan email valid untuk mailto
        $emailTo = $candidate['email'] && $candidate['email'] !== '-' ? $candidate['email'] : '';
        
        // Dapatkan path file CV yang benar
        $cvDownloadPath = null;
        if ($candidate['cv_path']) {
            $fullPath = storage_path('app/' . $candidate['cv_path']);
            if (file_exists($fullPath)) {
                $cvDownloadPath = asset('storage/' . $candidate['cv_path']);
            } elseif (file_exists($candidate['cv_path'])) {
                $cvDownloadPath = asset('storage/' . basename($candidate['cv_path']));
            }
        }
        
        // Nama file untuk resume download
        $resumeFileName = str_replace(' ', '_', ($candidate['name'] ?? 'Resume')) . '_CVision_Resume.txt';
    @endphp

    @if($emailTo)
        <a href="mailto:{{ $emailTo }}?subject=Re: {{ urlencode($candidate['position'] ?? 'Job Application') }}"
           target="_blank"
           class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#F0F2FF] hover:bg-[#E0E3FA] text-gray-700 font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Contact via Email
        </a>
    @else
        <span class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-gray-200 text-gray-400 font-semibold text-sm px-6 py-3 rounded-xl cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            No Email Available
        </span>
    @endif

    @if(!empty($candidate['resume_text']))
        {{-- Download AI-generated resume as .txt file --}}
        <a href="data:text/plain;charset=utf-8,{{ urlencode($candidate['resume_text']) }}"
           download="{{ $resumeFileName }}"
           class="flex-1 w-full sm:w-auto flex items-center justify-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Resume
        </a>
    @elseif($cvDownloadPath)
        {{-- Fallback: download original PDF --}}
        <a href="{{ $cvDownloadPath }}" 
           download="{{ $candidate['name'] ?? 'Resume' }}_CV.pdf"
           target="_blank"
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