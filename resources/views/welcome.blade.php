<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EDDY'S AI - AI English Learning Platform</title>

    {{-- Favicon --}}
    @include('_admin._layout.favicon')

    <!-- Fonts: Plus Jakarta Sans & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&family=Plus+Jakarta+Sans:ital,wght@0,500..800;1,500..800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-800 min-h-screen flex flex-col justify-between selection:bg-secondary selection:text-white">
    {{-- Header Navigation --}}
    <header class="w-full bg-white border-b border-slate-200 shadow-2xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo-academy.png') }}" alt="EDDY'S AI Academy" class="h-9 w-auto">
            </a>

            <div class="flex items-center gap-3">
                @auth
                    <x-admin.button href="{{ route('admin.dashboard') }}" size="sm" color="primary">
                        Dashboard Panel
                    </x-admin.button>
                @else
                    <x-admin.button href="{{ route('login') }}" size="sm" color="primary">
                        Masuk ke Portal
                    </x-admin.button>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="max-w-4xl w-full">
            <x-admin.card variant="white" padding="p-8 sm:p-12" class="text-center">
                {{-- Achievement Badge Pill --}}
                <div class="inline-flex items-center gap-2 mb-6">
                    <x-admin.badge variant="achievement" size="lg">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>Platform Pembelajaran Bahasa Inggris AI Bersertifikasi</span>
                    </x-admin.badge>
                </div>

                {{-- Heading --}}
                <h1 class="text-3xl sm:text-5xl font-black text-primary tracking-tight leading-tight max-w-2xl mx-auto">
                    Kuasai Bahasa Inggris Bersama Tutor AI Pribadi
                </h1>

                {{-- Subtitle --}}
                <p class="mt-4 text-sm sm:text-base text-slate-600 max-w-xl mx-auto leading-relaxed">
                    Belajar interaktif dengan kurikulum standar CEFR (A1-C2). Dilengkapi latihan speaking voice tutor real-time, bank soal teruji, dan sertifikasi resmi.
                </p>

                {{-- CTA Buttons --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <x-admin.button href="{{ route('admin.dashboard') }}" size="lg" color="secondary">
                            Lanjutkan Belajar & Pantau Dashboard
                        </x-admin.button>
                    @else
                        <x-admin.button href="{{ route('login') }}" size="lg" color="secondary">
                            Mulai Belajar Sekarang
                        </x-admin.button>
                        <x-admin.button href="{{ route('login') }}" size="lg" color="outline-secondary">
                            Masuk Akun Pengajar
                        </x-admin.button>
                    @endauth
                </div>

                {{-- 3 Feature Cards --}}
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
                    <div class="p-4 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300">
                        <div class="size-8 rounded-lg bg-primary text-white flex items-center justify-center font-black text-xs mb-2.5">
                            AI
                        </div>
                        <h4 class="font-extrabold text-xs sm:text-sm text-primary mb-1">AI Voice Tutor</h4>
                        <p class="text-xs text-slate-500 leading-normal">Latihan percakapan speaking dua arah tanpa takut salah dengan koreksi intonasi.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300">
                        <div class="size-8 rounded-lg bg-secondary text-white flex items-center justify-center font-black text-xs mb-2.5">
                            C1
                        </div>
                        <h4 class="font-extrabold text-xs sm:text-sm text-primary mb-1">Standar CEFR</h4>
                        <p class="text-xs text-slate-500 leading-normal">Kurikulum tersusun bertahap dari pemula A1 hingga kemahiran profesional C2.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300">
                        <div class="size-8 rounded-lg bg-accent text-primary flex items-center justify-center font-black text-xs mb-2.5">
                            ★
                        </div>
                        <h4 class="font-extrabold text-xs sm:text-sm text-primary mb-1">Sertifikat Kelulusan</h4>
                        <p class="text-xs text-slate-500 leading-normal">Uji kompetensi lewat final exam M13 dan dapatkan sertifikat resmi tervalidasi.</p>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="w-full py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
        <p>© {{ date('Y') }} EDDY'S AI English Learning Platform. All rights reserved.</p>
    </footer>
</body>
</html>
