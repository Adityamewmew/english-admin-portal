@extends('_admin._layout.app')

@section('title', 'Modul Kurikulum')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Katalog pembelajaran bahasa Inggris dari level A1 hingga C2">
        <x-admin.button href="{{ route('admin.modules.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Modul Baru
        </x-admin.button>
    </x-admin.page-header>

    {{-- Top Statistics Summary Cards using <x-admin.card> --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <x-admin.card fit="true">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Total Modul</p>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $stats['total'] ?? count($data) }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card fit="true">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Modul Pembelajaran</p>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['learning_count'] ?? 0 }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card fit="true">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Modul Ujian Akhir</p>
                    <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ $stats['exam_count'] ?? 0 }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
            </div>
        </x-admin.card>
    </div>

    {{-- CEFR Filter Pills using <x-admin.button> --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <x-admin.button href="{{ route('admin.modules.index', ['cefr' => 'all', 'keywords' => $keywords]) }}"
            size="sm"
            :color="$cefr === 'all' ? 'primary' : 'outline-secondary'"
            class="rounded-full !py-1 !px-3.5 text-xs font-semibold">
            Semua Level
        </x-admin.button>

        @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
            <x-admin.button href="{{ route('admin.modules.index', ['cefr' => $lvl, 'keywords' => $keywords]) }}"
                size="sm"
                :color="$cefr === $lvl ? 'primary' : 'outline-secondary'"
                class="rounded-full !py-1 !px-3.5 text-xs font-semibold">
                Level {{ $lvl }}
            </x-admin.button>
        @endforeach
    </div>

    {{-- Search Filter Form using <x-admin.input> and <x-admin.button> --}}
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

    {{-- Card Base Grid Layout using <x-admin.card> and <x-admin.button> --}}
    @php
        $cefrBadges = [
            'A1' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            'A2' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800',
            'B1' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
            'B2' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
            'C1' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
            'C2' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
        ];
        $fallbackIllustrations = [
            asset('illustrations/hero-learning.svg'),
            asset('illustrations/student-books.svg'),
            asset('illustrations/article-vocab.svg'),
            asset('illustrations/article-guide.svg'),
        ];
    @endphp

    @if(count($data) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($data as $m)
                @php
                    $isExam = !empty($m->is_exam) || str_contains($m->id, 'M13');
                @endphp
                <x-admin.card class="hover:border-blue-500/40 dark:hover:border-blue-400/40 transition-all duration-200">
                    <div class="h-full flex flex-col justify-between gap-3">
                        
                        {{-- Top Thematic Illustration / Banner Frame --}}
                        <div class="w-full h-32 rounded-xl flex items-center justify-center relative overflow-hidden border border-gray-100 dark:border-neutral-700/80 bg-slate-50 dark:bg-neutral-800/80 p-2 group-hover:scale-[1.01] transition-transform">
                            
                            {{-- Top-Left Badges: CEFR & ID --}}
                            <div class="absolute top-2.5 start-2.5 z-10 flex items-center gap-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full font-mono border shadow-2xs {{ $cefrBadges[$m->cefr] ?? 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700' }}">
                                    CEFR {{ $m->cefr }}
                                </span>
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-white/95 dark:bg-neutral-900/95 text-gray-700 dark:text-neutral-300 border border-gray-200/60 dark:border-neutral-700 shadow-2xs">
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
                                    <span>Ujian Akhir</span>
                                </div>
                            @else
                                <div class="absolute top-2.5 end-2.5 z-10 px-2 py-0.5 rounded-full bg-white/95 dark:bg-neutral-900/95 text-gray-600 dark:text-neutral-300 font-semibold text-[10px] border border-gray-200/60 dark:border-neutral-700 shadow-2xs">
                                    ±{{ $m->estimated_minutes ?? 15 }} mnt
                                </div>
                            @endif

                            {{-- Banner Image or Thematic Illustration --}}
                            @if(!empty($m->banner_url))
                                <img src="{{ $m->banner_url }}" alt="{{ $m->title }}"
                                    class="w-full h-full object-cover pointer-events-none rounded-lg" />
                            @else
                                <img src="{{ $fallbackIllustrations[$loop->index % count($fallbackIllustrations)] }}" alt="{{ $m->title }}"
                                    class="w-full h-full object-contain pointer-events-none drop-shadow-2xs p-2" />
                            @endif
                        </div>

                        {{-- Middle Body: Meta, Title & Description --}}
                        <div class="px-0.5 space-y-1.5 flex-1 flex flex-col justify-between">
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

                            {{-- Footer Actions using <x-admin.button> --}}
                            <div class="pt-3 mt-1.5 border-t border-gray-100 dark:border-neutral-700/80 flex items-center justify-between gap-1.5">
                                {{-- Primary CTA: Kelola Modul --}}
                                <x-admin.button href="{{ route('admin.modules.detail', $m->id) }}"
                                    size="sm"
                                    color="primary"
                                    class="flex-1 justify-center !py-2 !px-2.5 text-xs font-bold">
                                    <span>Kelola Modul</span>
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </x-admin.button>

                                {{-- Edit Button --}}
                                <x-admin.button href="{{ route('admin.modules.update', $m->id) }}"
                                    size="sm"
                                    color="outline-secondary"
                                    class="!p-2"
                                    title="Edit Modul">
                                    @include('_admin._layout.icons.pencil')
                                </x-admin.button>

                                {{-- Delete Button --}}
                                <form action="{{ route('admin.modules.delete', $m->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul {{ $m->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit"
                                        size="sm"
                                        color="outline-danger"
                                        class="!p-2 cursor-pointer"
                                        title="Hapus Modul">
                                        @include('_admin._layout.icons.trash')
                                    </x-admin.button>
                                </form>
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
    @else
        <x-admin.card fit="true">
            <div class="py-10 text-center">
                <x-admin.empty-state text="Tidak ada data modul kurikulum ditemukan untuk filter ini" />
            </div>
        </x-admin.card>
    @endif

    {{-- Pagination --}}
    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $data->links() }}
        </div>
    @endif
@endsection
