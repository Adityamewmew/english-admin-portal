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
    @endphp

    <div class="space-y-6">
        {{-- HERO SECTION --}}
        <x-admin.card variant="surface" padding="p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-2xl bg-white border border-slate-200 border-b-2 border-b-slate-300 text-xs font-semibold text-primary mb-4 shadow-2xs">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $today->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-primary dark:text-white leading-tight">
                        {{ $greeting }}, <span class="text-secondary">{{ Auth::user()->name }}</span>
                    </h1>

                    <p class="mt-2 text-sm sm:text-base leading-relaxed text-slate-600 dark:text-neutral-300 max-w-xl">
                        Selamat datang di Panel Administrasi <strong>EDDY'S AI English Learning</strong>. Pantau metrik siswa, kurikulum pembelajaran, dan bank soal dalam satu kendali terpusat.
                    </p>
                </div>

                <div class="flex flex-col items-start lg:items-end">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 border-b-4 border-b-slate-300 text-center shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Waktu Server</span>
                        <div class="text-3xl sm:text-4xl font-extrabold tracking-tight text-primary tabular-nums" id="dashboard-clock">
                            {{ $today->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.card>

        {{-- METRICS STATS --}}
        @if (!empty($stats))
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-admin.card variant="white" padding="p-5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Siswa</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white">
                        {{ number_format($stats['totalStudents']) }}
                    </div>
                    <div class="mt-2">
                        <x-admin.badge variant="success" size="sm">
                            <span class="size-1.5 rounded-full bg-emerald-600"></span>
                            Siswa aktif
                        </x-admin.badge>
                    </div>
                </x-admin.card>

                <x-admin.card variant="white" padding="p-5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Modul Kurikulum</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white">
                        {{ number_format($stats['totalModules']) }}
                    </div>
                    <div class="mt-2">
                        <x-admin.badge variant="neutral" size="sm">
                            Level A1 s/d C2
                        </x-admin.badge>
                    </div>
                </x-admin.card>

                <x-admin.card variant="white" padding="p-5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Butir Soal (Bank Soal)</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white">
                        {{ number_format($stats['totalQuestions']) }}
                    </div>
                    <div class="mt-2">
                        <x-admin.badge variant="neutral" size="sm">
                            Placement & Readiness
                        </x-admin.badge>
                    </div>
                </x-admin.card>

                <x-admin.card variant="white" padding="p-5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Modul Selesai</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-secondary dark:text-secondary-light">
                        {{ number_format($stats['totalCompleted']) }}
                    </div>
                    <div class="mt-2">
                        <x-admin.badge variant="secondary" size="sm">
                            Penyelesaian materi
                        </x-admin.badge>
                    </div>
                </x-admin.card>
            </section>

            {{-- CEFR Distribution Bar --}}
            <x-admin.card variant="surface" padding="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-extrabold text-primary dark:text-white">
                        Distribusi Level Siswa (CEFR Distribution)
                    </h3>
                    <x-admin.badge variant="achievement" size="sm">
                        Total 6 Level CEFR
                    </x-admin.badge>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                    @foreach ($stats['cefrDistribution'] as $lvl => $count)
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 border-b-3 border-b-slate-300 text-center shadow-2xs transition-all hover:-translate-y-0.5">
                            <x-admin.badge :cefr="$lvl" size="sm" class="mb-1.5">
                                {{ $lvl }}
                            </x-admin.badge>
                            <div class="text-xl font-extrabold text-primary dark:text-neutral-100">{{ $count }}</div>
                            <span class="text-[10px] text-slate-400 font-medium">Siswa</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        @endif

        {{-- MODUL APLIKASI NAVIGATION --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-primary dark:text-white">
                    Modul Aplikasi Utama
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($modules as $index => $menu)
                    @php
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
                       class="group p-5 rounded-2xl bg-white border border-slate-200 border-b-4 border-b-slate-300 hover:border-b-primary hover:border-slate-300 transition-all duration-150 flex items-start gap-4 shadow-2xs hover:-translate-y-0.5 active:translate-y-0.5">
                        <div class="size-12 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white group-hover:border-primary-dark transition-colors [&_svg]:size-6">
                            @if ($menu->icon)
                                @include($menu->icon)
                            @endif
                        </div>
                        <div class="grow min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-base font-extrabold text-primary group-hover:text-secondary transition-colors truncate">
                                    {{ $menu->label }}
                                </h3>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-slate-300 group-hover:text-secondary group-hover:translate-x-1 transition-all shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 line-clamp-2">
                                {{ $moduleDesc }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full">
                        <x-admin.empty-state message="Tidak ada modul tersedia untuk akun Anda." />
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @push('scripts')
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