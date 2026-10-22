@extends('_admin._layout.app')

@section('title', 'Bank Soal (Item Bank)')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Koleksi butir soal ujian penempatan (placement test) dan asesmen CEFR">
        <x-admin.button href="{{ route('admin.questions.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Butir Soal
        </x-admin.button>
    </x-admin.page-header>

    {{-- Filters --}}
    <x-admin.card variant="white" padding="p-4 sm:p-5" class="mb-6 space-y-3.5">
        {{-- CEFR Pills --}}
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-slate-500 mr-1">Level:</span>
            <a navigate href="{{ route('admin.questions.index', ['cefr' => 'all', 'skill' => $skill, 'keywords' => $keywords]) }}"
                class="px-2.5 py-1 rounded-xl text-xs font-bold transition-all {{ $cefr === 'all' ? 'bg-primary text-white border-b-2 border-primary-dark shadow-xs' : 'bg-surface text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                Semua Level
            </a>
            @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
                <a navigate href="{{ route('admin.questions.index', ['cefr' => $lvl, 'skill' => $skill, 'keywords' => $keywords]) }}"
                    class="px-2.5 py-1 rounded-xl text-xs font-bold transition-all {{ $cefr === $lvl ? 'bg-primary text-white border-b-2 border-primary-dark shadow-xs' : 'bg-surface text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                    {{ $lvl }}
                </a>
            @endforeach
        </div>

        {{-- Skill & Search form --}}
        <form action="{{ route('admin.questions.index') }}" method="GET" navigate-form class="flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="cefr" value="{{ $cefr }}">
            <div class="w-full sm:w-44">
                <select name="skill" onchange="this.form.submit()" class="py-2 px-3 block w-full bg-white border border-slate-300 rounded-[10px] text-xs text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white cursor-pointer">
                    <option value="all" {{ $skill === 'all' ? 'selected' : '' }}>Semua Skill</option>
                    <option value="mcq" {{ in_array($skill, ['mcq', 'grammar', 'vocabulary']) ? 'selected' : '' }}>MCQ (Grammar / Vocab)</option>
                    <option value="listening" {{ $skill === 'listening' ? 'selected' : '' }}>Listening</option>
                    <option value="speaking" {{ $skill === 'speaking' ? 'selected' : '' }}>Speaking</option>
                    <option value="writing" {{ $skill === 'writing' ? 'selected' : '' }}>Writing</option>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari pertanyaan, jawaban, ID..." size="sm" />
            </div>
            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary">
                    @include('_admin._layout.icons.search')
                    Cari
                </x-admin.button>
                @if (!empty($keywords) || $cefr !== 'all' || $skill !== 'all')
                    <x-admin.button href="{{ route('admin.questions.index') }}" size="sm" color="outline-secondary">
                        @include('_admin._layout.icons.reset')
                        Reset
                    </x-admin.button>
                @endif
            </div>
        </form>
    </x-admin.card>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th class="min-w-[260px]">ID & Pertanyaan</x-admin.table.th>
                    <x-admin.table.th class="w-28 text-center" align="center">Skill</x-admin.table.th>
                    <x-admin.table.th class="w-20 text-center" align="center">CEFR</x-admin.table.th>
                    <x-admin.table.th class="w-56 max-w-[240px]">Kunci Jawaban</x-admin.table.th>
                    <x-admin.table.th class="w-32 text-end sticky end-0 bg-surface/95 dark:bg-neutral-800/95 backdrop-blur-xs z-10 shadow-[-6px_0_10px_rgba(0,0,0,0.03)]" align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $q)
                    <x-admin.table.tr>
                        <x-admin.table.td innerClass="px-5 py-3">
                            <div class="space-y-1">
                                <span class="font-mono text-[11px] font-bold text-primary bg-surface px-1.5 py-0.5 rounded border border-slate-200 inline-block">
                                    {{ $q->id }}
                                </span>
                                <p class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white line-clamp-2 leading-snug">
                                    {{ $q->question }}
                                </p>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-3 py-3 text-center">
                            <x-admin.badge variant="secondary" size="sm" class="capitalize">
                                {{ $q->skill }}
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-3 py-3 text-center">
                            <x-admin.badge :cefr="$q->cefr" size="sm">
                                {{ $q->cefr }}
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-5 py-3">
                            <div class="max-w-[220px] truncate" title="{{ $q->answer }}">
                                <x-admin.badge variant="success" size="sm" class="font-mono truncate max-w-full inline-block">
                                    {{ $q->answer }}
                                </x-admin.badge>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td class="sticky end-0 bg-white dark:bg-neutral-800 z-10 shadow-[-6px_0_10px_rgba(0,0,0,0.03)]" innerClass="px-4 py-2 flex items-center justify-end gap-x-1.5">
                            <x-admin.button href="{{ route('admin.questions.detail', $q->id) }}" size="sm" color="outline-secondary" title="Detail">
                                @include('_admin._layout.icons.view_detail')
                            </x-admin.button>
                            <x-admin.button href="{{ route('admin.questions.update', $q->id) }}" size="sm" color="outline-secondary" title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </x-admin.button>
                            <form action="{{ route('admin.questions.delete', $q->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus butir soal ini?')">
                                @csrf
                                @method('DELETE')
                                <x-admin.button type="submit" size="sm" color="outline-danger" title="Hapus">
                                    @include('_admin._layout.icons.trash')
                                </x-admin.button>
                            </form>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="5" class="text-center py-8">
                            <x-admin.empty-state message="Tidak ada butir soal ditemukan." />
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>
    </x-admin.table.wrapper>

    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    @endif
@endsection
