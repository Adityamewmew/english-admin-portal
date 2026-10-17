@extends('_admin._layout.app')

@section('title', 'Sub Modules')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Katalog sub-modul pembelajaran per keahlian bahasa (Reading, Listening, Speaking, Grammar)">
        <x-admin.button href="{{ route('admin.sub_modules.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Sub-Modul
        </x-admin.button>
    </x-admin.page-header>

    {{-- Filter Bar --}}
    <div class="mb-6 bg-white dark:bg-neutral-800 p-4 rounded-2xl border border-gray-200 dark:border-neutral-700 space-y-3">
        <form action="{{ route('admin.sub_modules.index') }}" method="GET" navigate-form class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">Filter Modul Induk</label>
                <select name="module_id" class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-xs dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">
                    <option value="all">Semua Modul</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod->id }}" {{ $moduleId === $mod->id ? 'selected' : '' }}>
                            [{{ $mod->cefr }}] {{ $mod->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">Skill / Kategori</label>
                <select name="skill_type" class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-xs dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">
                    <option value="all">Semua Skill</option>
                    @foreach (['reading', 'listening', 'speaking', 'grammar', 'vocabulary', 'writing'] as $sk)
                        <option value="{{ $sk }}" {{ $skillType === $sk ? 'selected' : '' }}>
                            {{ ucfirst($sk) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">Pencarian</label>
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari judul sub-modul..." size="sm" />
            </div>

            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary" class="w-full justify-center">
                    @include('_admin._layout.icons.search')
                    Filter
                </x-admin.button>
                @if (!empty($keywords) || $moduleId !== 'all' || $skillType !== 'all')
                    <x-admin.button href="{{ route('admin.sub_modules.index') }}" size="sm" color="outline-secondary">
                        @include('_admin._layout.icons.reset')
                    </x-admin.button>
                @endif
            </div>
        </form>
    </div>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Sub-Modul</x-admin.table.th>
                    <x-admin.table.th>Modul Induk</x-admin.table.th>
                    <x-admin.table.th>Skill</x-admin.table.th>
                    <x-admin.table.th>Konten Bagian</x-admin.table.th>
                    <x-admin.table.th>Urutan</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $sm)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <div class="space-y-0.5">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white block">
                                    {{ $sm->title }}
                                </span>
                                <span class="font-mono text-[10px] text-gray-400">
                                    ID: {{ $sm->id }}
                                </span>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold rounded bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                    {{ $sm->module_cefr ?? '-' }}
                                </span>
                                <span class="text-xs text-gray-700 dark:text-neutral-300 line-clamp-1 max-w-[200px]" title="{{ $sm->module_title }}">
                                    {{ $sm->module_title ?? $sm->module_id }}
                                </span>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @php
                                $badgeColor = match(strtolower($sm->skill_type)) {
                                    'speaking' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400',
                                    'listening' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400',
                                    'reading' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-400',
                                    'grammar' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-neutral-800 dark:text-neutral-300'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                {{ ucfirst($sm->skill_type) }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs text-gray-600 dark:text-neutral-300">
                                {{ $sm->sections_count ?? 0 }} Bagian
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-mono font-bold text-gray-500">
                                #{{ $sm->order_index }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end gap-x-2">
                            <a navigate href="{{ route('admin.sub_modules.detail', $sm->id) }}"
                                class="inline-flex items-center gap-1.5 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400" title="Detail">
                                @include('_admin._layout.icons.view_detail')
                                Detail
                            </a>
                            <a navigate href="{{ route('admin.sub_modules.update', $sm->id) }}"
                                class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400" title="Edit">
                                @include('_admin._layout.icons.pencil')
                                Edit
                            </a>
                            <form action="{{ route('admin.sub_modules.delete', $sm->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-modul ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 cursor-pointer" title="Delete">
                                    @include('_admin._layout.icons.trash')
                                    Hapus
                                </button>
                            </form>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="6" class="text-center py-8">
                            <x-admin.empty-state text="Tidak ada data sub-modul ditemukan" />
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
