@extends('layouts.app')

@section('title', config('app.name', 'CVision') . ' - Seleksi CV Lebih Cerdas')

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="bg-[#E8EAFF] px-8 py-20 md:py-28">
    <div class="max-w-3xl">
        <span class="inline-block bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-full mb-6 shadow-sm">
            Solusi Rekruitmen yang Lebih Cerdas
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-2">Seleksi CV.</h1>
        <h2 class="text-4xl md:text-5xl font-bold text-[#4B52B0] leading-tight mb-6">Lebih Akurat, Lebih Efisien.</h2>
        <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-8 max-w-xl">
            {{ config('app.name', 'CVision') }} membantu HRD menganalisis CV kandidat dan mencocokkan
            dengan kriteria lowongan secara otomatis. Hemat waktu, dapatkan kandidat terbaik.
        </p>
        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-2 bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold px-7 py-3.5 rounded-xl transition-colors shadow-md">
            Buat Akun Sekarang
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

{{-- ===================== CARA KERJA ===================== --}}
<section class="bg-white px-8 py-16 md:py-20">
    <div class="max-w-5xl mx-auto">
        <h3 class="text-2xl md:text-3xl font-bold text-[#4B52B0] text-center mb-14">Cara Kerja Sistem</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 relative">
            <div class="hidden md:block absolute top-8 left-[14%] right-[14%] h-px bg-gray-200 z-0"></div>

            @foreach ([
                ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'num' => 1, 'title' => 'Input Lowongan', 'desc' => 'Masukkan detail posisi dan kriteria yang dibutuhkan', 'highlight' => true],
                ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',  'num' => 2, 'title' => 'Upload CV Kandidat', 'desc' => 'Unggah satu atau banyak CV kandidat', 'highlight' => false],
                ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7', 'num' => 3, 'title' => 'Analisa & Penilaian', 'desc' => 'Sistem menganalisis dan memberi skor kecocokan secara otomatis', 'highlight' => false],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'num' => 4, 'title' => 'Lihat Ranking & Rekomendasi', 'desc' => 'HRD melihat hasil ranking dan rekomendasi kandidat terbaik', 'highlight' => false],
            ] as $step)
            <div class="flex flex-col items-center text-center z-10">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-6 h-6 rounded-full bg-[#4B52B0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $step['num'] }}</span>
                    <span class="font-semibold text-sm {{ $step['highlight'] ? 'text-[#4B52B0]' : 'text-gray-800' }}">{{ $step['title'] }}</span>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ===================== CTA BANNER ===================== --}}
<section class="px-6 pb-10">
    <div class="bg-[#3B44A9] px-8 py-10 rounded-2xl max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h4 class="text-white font-bold text-xl md:text-2xl mb-1">Siap Mempermudah Proses Rekruitmen?</h4>
            <p class="text-blue-200 text-sm">Bergabung sekarang dan temukan kandidat terbaik lebih cepat.</p>
        </div>
        <a href="{{ route('register') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 border-2 border-white text-white font-semibold px-6 py-3 rounded-xl hover:bg-white hover:text-[#3B44A9] transition-colors whitespace-nowrap">
            Daftar Akun
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

@endsection