@extends('_admin._layout.app')

@section('title', 'Bank Soal (Item Bank)')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Koleksi butir soal ujian penempatan (placement test) dan asesmen CEFR">
    </x-admin.page-header>

    {{-- Filters --}}
    <div class="mb-6 space-y-3">
        {{-- CEFR Pills --}}
        <div class="flex flex-wrap gap-2">
            <a navigate href="{{ route('admin.questions.index', ['cefr' => 'all', 'skill' => $skill, 'keywords' => $keywords]) }}"
                class="px-3 py-1 rounded-full text-xs font-semibold {{ $cefr === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300' }}">
                Semua Level
            </a>
            @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
                <a navigate href="{{ route('admin.questions.index', ['cefr' => $lvl, 'skill' => $skill, 'keywords' => $keywords]) }}"
                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $cefr === $lvl ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300' }}">
                    Level {{ $lvl }}
                </a>
            @endforeach
        </div>

        {{-- Skill & Search form --}}
        <form action="{{ route('admin.questions.index') }}" method="GET" navigate-form class="flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="cefr" value="{{ $cefr }}">
            <div class="w-full sm:w-44">
                <select name="skill" class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-xs dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">
                    <option value="all" {{ $skill === 'all' ? 'selected' : '' }}>Semua Skill</option>
                    <option value="grammar" {{ $skill === 'grammar' ? 'selected' : '' }}>Grammar</option>
                    <option value="vocabulary" {{ $skill === 'vocabulary' ? 'selected' : '' }}>Vocabulary</option>
                    <option value="listening" {{ $skill === 'listening' ? 'selected' : '' }}>Listening</option>
                    <option value="reading" {{ $skill === 'reading' ? 'selected' : '' }}>Reading</option>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari pertanyaan, jawaban, ID..." size="sm" />
            </div>
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
        </form>
    </div>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>ID & Pertanyaan</x-admin.table.th>
                    <x-admin.table.th>Skill</x-admin.table.th>
                    <x-admin.table.th>CEFR</x-admin.table.th>
                    <x-admin.table.th>Kunci Jawaban</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $q)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <div class="space-y-1 max-w-lg">
                                <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded">
                                    {{ $q->id }}
                                </span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                    {{ $q->question }}
                                </p>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold capitalize bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                {{ $q->skill }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                {{ $q->cefr }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-1 rounded border border-emerald-200 dark:border-emerald-800/50">
                                {{ $q->answer }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end">
                            <a navigate href="{{ route('admin.questions.detail', $q->id) }}"
                                class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-semibold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400">
                                @include('_admin._layout.icons.view_detail')
                                Detail Soal
                            </a>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="5" class="text-center py-8">
                            <x-admin.empty-state text="Tidak ada butir soal ditemukan" />
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
