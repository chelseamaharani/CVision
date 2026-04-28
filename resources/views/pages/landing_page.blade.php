@extends('layouts.app')

@section('title', 'CVision - Seleksi CV Lebih Cerdas')

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="bg-[#E8EAFF] px-6 md:px-12 pt-10 pb-12 md:pt-12 md:pb-16">
    <div class="max-w-7xl">

        <div class="max-w-2xl">

            <span class="inline-block bg-white/40 border-2 border-white text-black text-sm md:text-base font-medium px-4 py-2 rounded-2xl mb-6">
                Solusi Rekruitmen yang Lebih Cerdas
            </span>

            <h1 class="text-3xl md:text-4xl font-bold text-black leading-tight mb-2">
                Seleksi CV.
            </h1>

            <h2 class="text-3xl md:text-4xl font-bold text-[#3449B8] leading-tight mb-5">
                Lebih Akurat, Lebih Efisien.
            </h2>

            <p class="text-black text-sm md:text-base leading-relaxed mb-6 max-w-xl">
                CV Screening & Matching membantu HRD menganalisis CV kandidat
                dan mencocokkan dengan kriteria lowongan secara otomatis.
                Hemat waktu, dapatkan kandidat terbaik
            </p>

            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-[#2734B8] hover:bg-[#1F2B9A] text-white font-semibold px-8 py-3 rounded-lg transition-colors shadow-md">
                Buat Akun Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

        </div>

    </div>
</section>

{{-- ===================== CARA KERJA ===================== --}}
<section class="bg-white px-6 py-14 md:py-16">
    <div class="max-w-7xl mx-auto">

        <h3 class="text-2xl md:text-[28px] font-bold text-[#102A8C] text-center mb-12">
            Cara Kerja Sistem
        </h3>

        <div class="relative grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-8 items-start">

            <div class="hidden md:block absolute top-[35px] left-[9%] right-[9%] h-px bg-gray-400 z-0"></div>

            @foreach ([
                [
                    'icon' => 'M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2',
                    'num' => 1,
                    'title' => 'Input Lowongan',
                    'desc' => 'Masukkan detail posisi dan kriteria yang dibutuhkan'
                ],
                [
                    'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1 M16 8l-4-4m0 0L8 8m4-4v12',
                    'num' => 2,
                    'title' => 'Upload CV Kandidat',
                    'desc' => 'Unggah satu atau banyak CV kandidat'
                ],
                [
                    'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z M10 7v3m0 0v3m0-3h3m-3 0H7',
                    'num' => 3,
                    'title' => 'Analisa & Penilaian',
                    'desc' => 'Sistem menganalisis dan memberi skor kecocokan secara otomatis'
                ],
                [
                    'icon' => 'M17 20h5v-2a3 3 0 00-3-3h-1 M7 20H2v-2a3 3 0 013-3h1 M12 12a4 4 0 100-8 4 4 0 000 8z M5 20a7 7 0 0114 0',
                    'num' => 4,
                    'title' => 'Lihat Ranking & Rekomendasi',
                    'desc' => 'HRD melihat hasil ranking dan rekomendasi kandidat terbaik'
                ],
            ] as $step)

                <div class="relative z-10 flex flex-col items-center text-center">

                    <div class="w-[70px] h-[70px] rounded-full bg-[#F1F2F8] flex items-center justify-center mb-6 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                        </svg>
                    </div>

                    <div class="flex items-center justify-center gap-3 mb-4 min-h-[32px]">
                        <span class="w-8 h-8 rounded-full bg-[#3F51B5] text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                            {{ $step['num'] }}
                        </span>

                        <h4 class="text-sm md:text-base font-semibold text-black leading-snug">
                            {{ $step['title'] }}
                        </h4>
                    </div>

                    <p class="text-sm text-black leading-relaxed max-w-[235px]">
                        {{ $step['desc'] }}
                    </p>

                </div>

            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CTA BANNER ===================== --}}
<section class="bg-white px-6 pb-10">
    <div class="max-w-7xl mx-auto bg-[#3F51B5] rounded-lg px-8 md:px-10 py-5 md:py-6 flex flex-col md:flex-row items-center justify-between gap-5">

        <div>
            <h4 class="text-white font-bold text-xl md:text-2xl">
                Siap Mempermudah Proses Rekruitmen?
            </h4>

            <p class="text-white/90 text-sm md:text-base">
                Bergabung sekarang dan temukan kandidat terbaik lebih cepat.
            </p>
        </div>

        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-3 border border-white text-white font-semibold px-8 py-3 rounded-md hover:bg-white hover:text-[#3F51B5] transition-colors">
            Daftar Akun
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

    </div>
</section>

@endsection