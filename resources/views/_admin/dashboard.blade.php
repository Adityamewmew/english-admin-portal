@extends('_admin._layout.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $today = now();

        $greeting = match (true) {
            $today->hour < 11 => 'Selamat Pagi',
            $today->hour < 15 => 'Selamat Siang',
            $today->hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        $accentPalette = ['blue', 'emerald', 'purple', 'orange', 'indigo', 'violet'];

        $accent = [
            'blue' => [
                'bg' => 'from-blue-500/10 via-blue-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-blue-500 to-blue-600 text-white',
                'icon_shadow' => 'shadow-blue-500/25',
                'arrow_hover' => 'group-hover:text-blue-400',
                'glow' => 'group-hover:shadow-blue-500/10',
            ],
            'emerald' => [
                'bg' => 'from-emerald-500/10 via-emerald-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white',
                'icon_shadow' => 'shadow-emerald-500/25',
                'arrow_hover' => 'group-hover:text-emerald-400',
                'glow' => 'group-hover:shadow-emerald-500/10',
            ],
            'purple' => [
                'bg' => 'from-purple-500/10 via-purple-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-purple-500 to-purple-600 text-white',
                'icon_shadow' => 'shadow-purple-500/25',
                'arrow_hover' => 'group-hover:text-purple-400',
                'glow' => 'group-hover:shadow-purple-500/10',
            ],
            'orange' => [
                'bg' => 'from-orange-500/10 via-orange-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-orange-500 to-orange-600 text-white',
                'icon_shadow' => 'shadow-orange-500/25',
                'arrow_hover' => 'group-hover:text-orange-400',
                'glow' => 'group-hover:shadow-orange-500/10',
            ],
            'indigo' => [
                'bg' => 'from-indigo-500/10 via-indigo-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white',
                'icon_shadow' => 'shadow-indigo-500/25',
                'arrow_hover' => 'group-hover:text-indigo-400',
                'glow' => 'group-hover:shadow-indigo-500/10',
            ],
            'violet' => [
                'bg' => 'from-violet-500/10 via-violet-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-violet-500 to-violet-600 text-white',
                'icon_shadow' => 'shadow-violet-500/25',
                'arrow_hover' => 'group-hover:text-violet-400',
                'glow' => 'group-hover:shadow-violet-500/10',
            ],
        ];
    @endphp

    <div class="space-y-8">
        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-[32px] bg-white dark:bg-neutral-900 border border-white/20 dark:border-white/5 shadow-[0_10px_60px_rgba(0,0,0,0.06)]">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 w-[420px] h-[420px] bg-blue-500/10 blur-3xl rounded-full"
                     style="animation: blob1 8s ease-in-out infinite;"></div>
                <div class="absolute bottom-0 left-0 w-[320px] h-[320px] bg-indigo-500/10 blur-3xl rounded-full"
                     style="animation: blob2 10s ease-in-out infinite;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-violet-500/5 blur-3xl rounded-full"
                     style="animation: blob3 14s ease-in-out infinite;"></div>
            </div>

            <div class="relative px-8 lg:px-10 py-10 lg:py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-neutral-100 dark:bg-white/5 mb-5">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                                {{ $today->translatedFormat('l, d F Y') }}
                            </span>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-semibold tracking-tight text-neutral-900 dark:text-white leading-tight">
                            {{ $greeting }},
                            <span class="bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}
                            </span>
                        </h1>

                        <p class="mt-4 text-[15px] leading-7 text-neutral-500 dark:text-neutral-400 max-w-xl">
                            Selamat datang di Panel Administrasi <strong>EDDY'S AI English Learning</strong>. Pantau metrik siswa, kurikulum pembelajaran, dan bank soal dalam satu kendali terpusat.
                        </p>
                    </div>

                    <div class="flex flex-col items-start lg:items-end">
                        <div class="text-[72px] leading-none font-light tracking-[-4px] text-neutral-900 dark:text-white tabular-nums"
                             id="dashboard-clock">
                            {{ $today->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- METRICS STATS --}}
        @if (!empty($stats))
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 shadow-sm">
                    <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 block mb-1">Total Siswa Terdaftar</span>
                    <div class="text-3xl font-extrabold text-neutral-900 dark:text-white">{{ number_format($stats['totalStudents']) }}</div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 inline-block">Siswa aktif belajar</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 shadow-sm">
                    <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 block mb-1">Modul Kurikulum</span>
                    <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($stats['totalModules']) }}</div>
                    <span class="text-[11px] text-neutral-400 mt-1 inline-block">Level A1 s/d C2</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 shadow-sm">
                    <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 block mb-1">Butir Soal (Item Bank)</span>
                    <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400">{{ number_format($stats['totalQuestions']) }}</div>
                    <span class="text-[11px] text-neutral-400 mt-1 inline-block">Placement & readiness</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 shadow-sm">
                    <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 block mb-1">Modul Terselesaikan</span>
                    <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['totalCompleted']) }}</div>
                    <span class="text-[11px] text-neutral-400 mt-1 inline-block">Penyelesaian materi siswa</span>
                </div>
            </section>

            {{-- CEFR Distribution Bar --}}
            <section class="p-6 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-3">
                    Distribusi Level Siswa (CEFR Distribution)
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                    @foreach ($stats['cefrDistribution'] as $lvl => $count)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200/50 dark:border-neutral-700/50 text-center">
                            <span class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400 block">{{ $lvl }}</span>
                            <span class="text-lg font-bold text-neutral-800 dark:text-neutral-200">{{ $count }}</span>
                            <span class="text-[10px] text-neutral-400 block">Siswa</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- MENU --}}
        <section>
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Modul Aplikasi
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse ($modules as $index => $menu)
                    @php
                        $accentKey = $accentPalette[$index % count($accentPalette)];
                        $c = $accent[$accentKey];

                        try {
                            $moduleUrl = route($menu->route_name);
                        } catch (\Exception $e) {
                            $moduleUrl = '#';
                        }

                        $moduleDesc = match ($menu->route_name) {
                            'admin.users.index' => 'Kelola akun siswa, guru, dan administrator sistem.',
                            'admin.modules.index' => 'Inspeksi kurikulum pembelajaran lengkap level A1 sampai C2.',
                            'admin.questions.index' => 'Kelola bank butir soal placement test dan readiness asesmen.',
                            'admin.sidebar_menu.index' => 'Atur hak akses dan hierarki navigasi sidebar panel.',
                            default => 'Buka modul ' . $menu->label . '.',
                        };
                    @endphp

                    <a navigate
                       href="{{ $moduleUrl }}"
                       class="group relative overflow-hidden rounded-[28px] bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 p-7 transition-all duration-500 hover:-translate-y-1 hover:shadow-lg shadow-black/5 {{ $c['glow'] }}">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br {{ $c['bg'] }}"></div>
                        <div class="relative flex flex-col h-full">
                            <div class="flex items-start justify-between">
                                <div class="size-14 rounded-2xl {{ $c['icon'] }} flex items-center justify-center shadow-lg {{ $c['icon_shadow'] }} ring-1 ring-white/20 ring-inset transition-all duration-300 group-hover:shadow-xl group-hover:scale-[1.03] [&_svg]:size-6">
                                    @if ($menu->icon)
                                        @include($menu->icon)
                                    @endif
                                </div>
                                <div class="translate-x-0 group-hover:translate-x-1 transition duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-neutral-300 dark:text-neutral-600 transition-colors {{ $c['arrow_hover'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-8">
                                <h3 class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                                    {{ $menu->label }}
                                </h3>
                                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-400">
                                    {{ $moduleDesc }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-[28px] border border-dashed border-neutral-200 dark:border-neutral-700 p-10 text-center">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Tidak ada modul tersedia untuk akun Anda.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @push('scripts')
        <style>
            @keyframes blob1 {
                0%   { transform: translateX(0px); }
                20%  { transform: translateX(60px); }
                45%  { transform: translateX(-30px); }
                65%  { transform: translateX(80px); }
                80%  { transform: translateX(-50px); }
                100% { transform: translateX(0px); }
            }
            @keyframes blob2 {
                0%   { transform: translateX(0px); }
                25%  { transform: translateX(-70px); }
                50%  { transform: translateX(40px); }
                70%  { transform: translateX(-55px); }
                90%  { transform: translateX(30px); }
                100% { transform: translateX(0px); }
            }
            @keyframes blob3 {
                0%   { transform: translate(-50%, -50%) translateX(0px); }
                30%  { transform: translate(-50%, -50%) translateX(50px); }
                55%  { transform: translate(-50%, -50%) translateX(-60px); }
                75%  { transform: translate(-50%, -50%) translateX(35px); }
                100% { transform: translate(-50%, -50%) translateX(0px); }
            }
        </style>
        <script>
            (() => {
                const clock = document.getElementById('dashboard-clock');
                if (!clock) return;
                setInterval(() => {
                    const now = new Date();
                    clock.textContent = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }, 1000);
            })();
        </script>
    @endpush
@endsection