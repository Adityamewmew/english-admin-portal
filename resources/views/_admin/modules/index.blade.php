@extends('_admin._layout.app')

@section('title', 'Modul Kurikulum')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Katalog pembelajaran bahasa Inggris dari level A1 hingga C2">
        <x-admin.button href="{{ route('admin.modules.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Modul Baru
        </x-admin.button>
    </x-admin.page-header>

    {{-- Top Statistics Summary --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <div class="p-3.5 rounded-2xl bg-white border border-gray-200/80 shadow-xs dark:bg-neutral-800 dark:border-neutral-700/80 flex items-center justify-between">
            <div class="space-y-0.5">
                <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Total Modul</p>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $stats['total'] ?? count($data) }}</p>
            </div>
            <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-gray-200/80 shadow-xs dark:bg-neutral-800 dark:border-neutral-700/80 flex items-center justify-between">
            <div class="space-y-0.5">
                <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Modul Pembelajaran</p>
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['learning_count'] ?? 0 }}</p>
            </div>
            <div class="size-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-white border border-gray-200/80 shadow-xs dark:bg-neutral-800 dark:border-neutral-700/80 flex items-center justify-between">
            <div class="space-y-0.5">
                <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Modul Ujian Akhir</p>
                <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400">{{ $stats['exam_count'] ?? 0 }}</p>
            </div>
            <div class="size-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- CEFR Filter Pills --}}
    <div class="mb-4 flex flex-wrap gap-2">
        <a navigate href="{{ route('admin.modules.index', ['cefr' => 'all', 'keywords' => $keywords]) }}"
            class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $cefr === 'all' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300' }}">
            Semua Level
        </a>
        @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
            <a navigate href="{{ route('admin.modules.index', ['cefr' => $lvl, 'keywords' => $keywords]) }}"
                class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $cefr === $lvl ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300' }}">
                Level {{ $lvl }}
            </a>
        @endforeach
    </div>

    {{-- Search Filter --}}
    <div class="mb-6">
        <form action="{{ route('admin.modules.index') }}" method="GET" navigate-form class="flex items-center gap-3">
            <input type="hidden" name="cefr" value="{{ $cefr }}">
            <div class="w-full sm:w-80">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari judul modul, topik, atau ID..." size="sm" />
            </div>
            <x-admin.button type="submit" size="sm" color="primary">
                @include('_admin._layout.icons.search')
                Cari
            </x-admin.button>
            @if (!empty($keywords) || $cefr !== 'all')
                <x-admin.button href="{{ route('admin.modules.index') }}" size="sm" color="outline-secondary">
                    @include('_admin._layout.icons.reset')
                    Reset
                </x-admin.button>
            @endif
        </form>
    </div>

    {{-- Card Base Grid Layout --}}
    @php
        $cardStyles = [
            ['bg' => '#F8FAFC', 'border' => '#E2E8F0', 'img' => asset('illustrations/hero-learning.svg'), 'badge' => 'bg-[#0D3B66] text-white'],
            ['bg' => '#FFFBEB', 'border' => '#FDE68A', 'img' => asset('illustrations/student-books.svg'), 'badge' => 'bg-[#F4B41A] text-[#0F172A]'],
            ['bg' => '#FEF2F2', 'border' => '#FECACA', 'img' => asset('illustrations/article-vocab.svg'), 'badge' => 'bg-[#9E1B1B] text-white'],
            ['bg' => '#F0F9FF', 'border' => '#BAE6FD', 'img' => asset('illustrations/article-guide.svg'), 'badge' => 'bg-[#1B4965] text-white'],
        ];
    @endphp

    @if(count($data) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($data as $m)
                @php
                    $isExam = !empty($m->is_exam) || str_contains($m->id, 'M13');
                    $style = $cardStyles[$loop->index % count($cardStyles)];
                @endphp
                <div class="group bg-white dark:bg-neutral-800 border border-gray-200/90 dark:border-neutral-700/80 rounded-2xl p-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md hover:border-blue-500/50 dark:hover:border-blue-400/50 transition-all duration-200 flex flex-col justify-between gap-3.5">
                    
                    {{-- Top Thematic Illustration Banner Frame --}}
                    <div style="background-color: {{ $style['bg'] }}"
                        class="w-full h-32 rounded-xl flex items-center justify-center relative overflow-hidden border border-black/5 p-2 group-hover:scale-[1.01] transition-transform">
                        
                        {{-- Top-Left Badges: CEFR & ID --}}
                        <div class="absolute top-2.5 start-2.5 z-10 flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full font-mono shadow-2xs {{ $style['badge'] }}">
                                CEFR {{ $m->cefr }}
                            </span>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-white/90 dark:bg-neutral-900/90 text-gray-700 dark:text-neutral-300 shadow-2xs">
                                {{ $m->id }}
                            </span>
                        </div>

                        {{-- Top-Right Badge: Exam vs Estimated Duration --}}
                        @if($isExam)
                            <div class="absolute top-2.5 end-2.5 z-10 px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-bold text-[10px] flex items-center gap-1 shadow-2xs">
                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                <span>Ujian</span>
                            </div>
                        @else
                            <div class="absolute top-2.5 end-2.5 z-10 px-2 py-0.5 rounded-full bg-white/90 dark:bg-neutral-900/90 text-gray-600 dark:text-neutral-400 font-semibold text-[10px] shadow-2xs">
                                ±{{ $m->estimated_minutes ?? 15 }} mnt
                            </div>
                        @endif

                        {{-- Illustration Image --}}
                        <img src="{{ $style['img'] }}" alt="{{ $m->title }}"
                            class="w-full h-full object-contain pointer-events-none drop-shadow-2xs" />
                    </div>

                    {{-- Middle Body: Meta, Title & Description --}}
                    <div class="px-1 space-y-1.5 flex-1 flex flex-col justify-between">
                        <div>
                            {{-- Meta Row --}}
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 dark:text-neutral-400 mb-1">
                                <span class="inline-flex items-center gap-1 text-gray-600 dark:text-neutral-300">
                                    <svg class="size-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    {{ $m->sections_count ?? 0 }} Bagian Materi
                                </span>
                                <span class="font-mono text-gray-400 dark:text-neutral-500">
                                    Pass: {{ $m->passing_score ?? 70 }}%
                                </span>
                            </div>

                            {{-- Title --}}
                            <h3 class="font-extrabold text-sm text-gray-900 dark:text-white leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $m->title }}
                            </h3>

                            {{-- Objective / Description --}}
                            @if(!empty($m->objective))
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $m->objective }}
                                </p>
                            @endif
                        </div>

                        {{-- Footer Actions --}}
                        <div class="pt-3 mt-2 border-t border-gray-100 dark:border-neutral-700/80 flex items-center justify-between gap-2">
                            {{-- Primary CTA: Kelola Modul --}}
                            <a navigate href="{{ route('admin.modules.detail', $m->id) }}"
                                class="flex-1 py-2 px-3 text-xs font-bold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white dark:bg-blue-950/40 dark:text-blue-300 dark:hover:bg-blue-600 dark:hover:text-white transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                                <span>Kelola Modul</span>
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>

                            {{-- Edit Button --}}
                            <a navigate href="{{ route('admin.modules.update', $m->id) }}"
                                class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 transition-all"
                                title="Edit Modul">
                                @include('_admin._layout.icons.pencil')
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('admin.modules.delete', $m->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul {{ $m->title }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 transition-all cursor-pointer"
                                    title="Hapus Modul">
                                    @include('_admin._layout.icons.trash')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-12 bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200/80 dark:border-neutral-700/80 text-center">
            <x-admin.empty-state text="Tidak ada data modul kurikulum ditemukan untuk filter ini" />
        </div>
    @endif

    {{-- Pagination --}}
    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $data->links() }}
        </div>
    @endif
@endsection
