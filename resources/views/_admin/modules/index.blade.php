@extends('_admin._layout.app')

@section('title', 'Modul Kurikulum')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Katalog pembelajaran bahasa Inggris dari level A1 hingga C2">
    </x-admin.page-header>

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

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Modul & Topik</x-admin.table.th>
                    <x-admin.table.th>CEFR</x-admin.table.th>
                    <x-admin.table.th>Struktur</x-admin.table.th>
                    <x-admin.table.th>Durasi</x-admin.table.th>
                    <x-admin.table.th>Tipe</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $m)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded">
                                        {{ $m->id }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $m->title }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-1">
                                    {{ $m->objective }}
                                </p>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                {{ $m->cefr }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-medium text-gray-600 dark:text-neutral-300">
                                {{ $m->sections_count ?? 0 }} Bagian Materi
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs text-gray-500 dark:text-neutral-400">
                                ±{{ $m->estimated_minutes ?? 15 }} mnt
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if (!empty($m->is_exam))
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    Ujian
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-neutral-800 dark:text-neutral-300">
                                    Materi
                                </span>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end">
                            <a navigate href="{{ route('admin.modules.detail', $m->id) }}"
                                class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs font-semibold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400">
                                @include('_admin._layout.icons.view_detail')
                                Inspeksi Materi
                            </a>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="6" class="text-center py-8">
                            <x-admin.empty-state text="Tidak ada data modul kurikulum ditemukan" />
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
