@extends('_admin._layout.app')

@section('title', 'Modul Kurikulum')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Katalog kurikulum pembelajaran bahasa Inggris dari level A1 hingga C2">
        <x-admin.button href="{{ route('admin.modules.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Modul Baru
        </x-admin.button>
    </x-admin.page-header>

    {{-- Top Statistics Summary Cards --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-admin.card variant="white" padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Modul</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white mt-1">{{ $stats['total'] ?? count($data) }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-surface border border-slate-200 border-b-2 border-b-slate-300 text-primary flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card variant="white" padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Modul Pembelajaran</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['learning_count'] ?? 0 }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-emerald-50 border border-emerald-200 border-b-2 border-b-emerald-300 text-emerald-600 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card variant="white" padding="p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Modul Ujian Akhir</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-secondary dark:text-secondary-light mt-1">{{ $stats['exam_count'] ?? 0 }}</p>
                </div>
                <div class="size-11 rounded-2xl bg-orange-50 border border-orange-200 border-b-2 border-b-secondary text-secondary flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
            </div>
        </x-admin.card>
    </div>

    {{-- Filter & Search Form --}}
    <x-admin.card variant="white" padding="p-4 sm:p-5" class="mb-6">
        <form action="{{ route('admin.modules.index') }}" method="GET" navigate-form class="flex flex-wrap items-center justify-between gap-4">
            <input type="hidden" name="cefr" value="{{ $cefr }}">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-xs font-bold text-slate-500 mr-1">Level CEFR:</span>
                <a href="{{ route('admin.modules.index', ['cefr' => 'all', 'keywords' => $keywords]) }}"
                   class="px-2.5 py-1 text-xs rounded-xl font-bold transition-all {{ $cefr === 'all' ? 'bg-primary text-white border-b-2 border-primary-dark shadow-xs' : 'bg-surface text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                    Semua Level
                </a>
                @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
                    <a href="{{ route('admin.modules.index', ['cefr' => $lvl, 'keywords' => $keywords]) }}"
                       class="px-2.5 py-1 text-xs rounded-xl font-bold transition-all {{ $cefr === $lvl ? 'bg-primary text-white border-b-2 border-primary-dark shadow-xs' : 'bg-surface text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                        {{ $lvl }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari judul modul atau topik..." size="sm" />
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
            </div>
        </form>
    </x-admin.card>

    @php
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
                <x-admin.card variant="white" padding="p-4" class="hover:border-primary/50 transition-all duration-150 flex flex-col justify-between">
                    <div class="space-y-3 flex-1 flex flex-col justify-between">
                        {{-- Top Thumbnail Frame --}}
                        <div class="w-full h-32 rounded-xl flex items-center justify-center relative overflow-hidden border border-slate-200 bg-surface p-2">
                            {{-- Top-Left Badges: CEFR & ID --}}
                            <div class="absolute top-2.5 start-2.5 z-10 flex items-center gap-1.5">
                                <x-admin.badge :cefr="$m->cefr" size="sm">
                                    {{ $m->cefr }}
                                </x-admin.badge>
                                <x-admin.badge variant="neutral" size="sm" class="bg-white/90">
                                    {{ $m->id }}
                                </x-admin.badge>
                            </div>

                            {{-- Top-Right Badge: Exam vs Estimated Duration --}}
                            @if($isExam)
                                <div class="absolute top-2.5 end-2.5 z-10">
                                    <x-admin.badge variant="secondary" size="sm">
                                        Ujian Akhir
                                    </x-admin.badge>
                                </div>
                            @else
                                <div class="absolute top-2.5 end-2.5 z-10">
                                    <x-admin.badge variant="neutral" size="sm" class="bg-white/90">
                                        ±{{ $m->estimated_minutes ?? 15 }} mnt
                                    </x-admin.badge>
                                </div>
                            @endif

                            {{-- Banner Image or Illustration --}}
                            @if(!empty($m->banner_url))
                                <img src="{{ $m->banner_url }}" alt="{{ $m->title }}"
                                    class="w-full h-full object-cover pointer-events-none rounded-lg" />
                            @else
                                <img src="{{ $fallbackIllustrations[$loop->index % count($fallbackIllustrations)] }}" alt="{{ $m->title }}"
                                    class="w-full h-full object-contain pointer-events-none p-2" />
                            @endif
                        </div>

                        {{-- Middle Body --}}
                        <div class="space-y-1.5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 mb-1">
                                    <span class="inline-flex items-center gap-1 text-slate-600 dark:text-neutral-300">
                                        {{ $m->sections_count ?? 0 }} Bagian Materi
                                    </span>
                                    <span class="font-mono text-slate-400">
                                        Pass: {{ $m->passing_score ?? 70 }}%
                                    </span>
                                </div>

                                <h3 class="font-extrabold text-sm text-primary dark:text-white leading-snug line-clamp-2">
                                    {{ $m->title }}
                                </h3>

                                @if(!empty($m->objective))
                                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $m->objective }}
                                    </p>
                                @endif
                            </div>

                            {{-- Footer Actions --}}
                            <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between gap-1.5">
                                <x-admin.button href="{{ route('admin.modules.detail', $m->id) }}"
                                    size="sm"
                                    color="primary"
                                    class="flex-1 justify-center text-xs">
                                    <span>Kelola</span>
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </x-admin.button>

                                <x-admin.button href="{{ route('admin.modules.update', $m->id) }}"
                                    size="sm"
                                    color="outline-secondary"
                                    title="Edit Modul">
                                    @include('_admin._layout.icons.pencil')
                                </x-admin.button>

                                <form action="{{ route('admin.modules.delete', $m->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul {{ $m->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit"
                                        size="sm"
                                        color="outline-danger"
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
        <x-admin.card variant="white" padding="p-10">
            <x-admin.empty-state message="Tidak ada data modul kurikulum ditemukan untuk filter ini." />
        </x-admin.card>
    @endif

    {{-- Pagination --}}
    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $data->links() }}
        </div>
    @endif
@endsection
