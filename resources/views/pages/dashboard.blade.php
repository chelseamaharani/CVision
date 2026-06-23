@extends('layouts.dashboard')

@section('title', 'Dashboard - CVision')

@section('content')

{{-- Badge --}}
<span class="inline-block bg-white border border-[#7B82C9]/40 text-gray-800 text-sm font-semibold px-5 py-2.5 rounded-full mb-8 shadow-sm">
    Smarter Recruitment Solutions
</span>

{{-- Headline --}}
<h1 class="text-4xl font-bold text-gray-900 leading-tight mb-1">
    CV Selection.
</h1>
<h2 class="text-4xl font-bold text-[#4B52B0] leading-tight mb-5">
    More Accurate, More Efficient.
</h2>

<p class="text-gray-600 text-base leading-relaxed mb-8 max-w-2xl">
    CV Screening & Matching helps HR analyze candidate CVs and automatically match them
    to job requirements. Save time and find the best candidates.
</p>

{{-- Feature Icons --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-10 max-w-3xl">

    <div class="group">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3 transition-transform duration-200 group-hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v2m0 16v2M2 12h2m16 0h2"/>
                <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
            </svg>
        </div>
        <p class="text-[#4B52B0] font-semibold text-base mb-1">Accurate</p>
        <p class="text-gray-500 text-sm leading-relaxed">CV analysis with AI and the right criteria</p>
    </div>

    <div class="group">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3 transition-transform duration-200 group-hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <p class="text-[#4B52B0] font-semibold text-base mb-1">Trusted</p>
        <p class="text-gray-500 text-sm leading-relaxed">Data security is guaranteed</p>
    </div>

    <div class="group">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3 transition-transform duration-200 group-hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <p class="text-[#4B52B0] font-semibold text-base mb-1">Fast Process</p>
        <p class="text-gray-500 text-sm leading-relaxed">Analysis results in quick counts</p>
    </div>

</div>

{{-- CTA: Start Screening Now --}}
<a href="{{ route('job_listing.create') }}"
   class="inline-flex items-center gap-2 bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold text-base px-7 py-3.5 rounded-xl transition-colors shadow-md mb-16">
    Start Screening Now
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
    </svg>
</a>

{{-- ===================== HOW THE SYSTEM WORKS ===================== --}}
<div class="mb-12">
    <h3 class="text-2xl font-bold text-[#4B52B0] text-center mb-12">How the System Works</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12 max-w-4xl mx-auto">

        {{-- Step 1 --}}
        <div class="flex flex-col items-center text-center cursor-pointer transition-transform duration-200 hover:-translate-y-1.5">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full bg-[#4B52B0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                <span class="font-semibold text-gray-800 text-sm">Input Vacancies</span>
            </div>
            <p class="text-gray-500 text-sm leading-relaxed">Enter position details and required criteria.</p>
        </div>

        {{-- Step 2 --}}
        <div class="flex flex-col items-center text-center cursor-pointer transition-transform duration-200 hover:-translate-y-1.5">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full bg-[#4B52B0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                <span class="font-semibold text-gray-800 text-sm">Candidates Apply</span>
            </div>
            <p class="text-gray-500 text-sm leading-relaxed">Candidates submit their CVs through the system</p>
        </div>

        {{-- Step 3 --}}
        <div class="flex flex-col items-center text-center cursor-pointer transition-transform duration-200 hover:-translate-y-1.5">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full bg-[#4B52B0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                <span class="font-semibold text-gray-800 text-sm">Analysis & Assessment</span>
            </div>
            <p class="text-gray-500 text-sm leading-relaxed">The system automatically analyzes and scores matches.</p>
        </div>

        {{-- Step 4 --}}
        <div class="flex flex-col items-center text-center cursor-pointer transition-transform duration-200 hover:-translate-y-1.5">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full bg-[#4B52B0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">4</span>
                <span class="font-semibold text-gray-800 text-sm">View Ranking & Recommendations</span>
            </div>
            <p class="text-gray-500 text-sm leading-relaxed">HR reviews the ranking results and recommendations for the best candidates.</p>
        </div>

    </div>
</div>

{{-- ===================== CTA BANNER ===================== --}}
<div class="bg-[#3B44A9] px-8 py-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <h4 class="text-white font-bold text-lg md:text-xl mb-1">
            Ready to Simplify the Recruitment Process?
        </h4>
        <p class="text-blue-200 text-sm">
            Use AI to analyze CVs and speed up your company's recruitment process.
        </p>
    </div>
    <a href="{{ route('job_listing.create') }}"
       class="flex-shrink-0 inline-flex items-center gap-2 bg-white text-[#3B44A9] font-semibold px-6 py-2.5 rounded-xl hover:bg-gray-100 transition-colors whitespace-nowrap text-sm">
        Try the System Now
    </a>
</div>

@endsection