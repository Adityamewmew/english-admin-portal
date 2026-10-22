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
    <x-admin.card variant="white" padding="p-4 sm:p-5" class="mb-6">
        <form action="{{ route('admin.sub_modules.index') }}" method="GET" navigate-form class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-neutral-300 mb-1">Filter Modul Induk</label>
                <select name="module_id" class="py-2 px-3 block w-full bg-white border border-slate-300 rounded-[10px] text-xs text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">
                    <option value="all">Semua Modul</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod->id }}" {{ $moduleId === $mod->id ? 'selected' : '' }}>
                            [{{ $mod->cefr }}] {{ $mod->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-neutral-300 mb-1">Skill / Kategori</label>
                <select name="skill_type" class="py-2 px-3 block w-full bg-white border border-slate-300 rounded-[10px] text-xs text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">
                    <option value="all">Semua Skill</option>
                    @foreach (['reading', 'listening', 'speaking', 'grammar', 'vocabulary', 'writing'] as $sk)
                        <option value="{{ $sk }}" {{ $skillType === $sk ? 'selected' : '' }}>
                            {{ ucfirst($sk) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-neutral-300 mb-1">Pencarian</label>
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
    </x-admin.card>

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
                                <span class="text-xs sm:text-sm font-extrabold text-primary dark:text-white block">
                                    {{ $sm->title }}
                                </span>
                                <span class="font-mono text-[10px] text-slate-400">
                                    ID: {{ $sm->id }}
                                </span>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <div class="flex items-center gap-1.5">
                                <x-admin.badge :cefr="$sm->module_cefr ?? 'A1'" size="sm">
                                    {{ $sm->module_cefr ?? '-' }}
                                </x-admin.badge>
                                <span class="text-xs text-slate-700 dark:text-neutral-300 line-clamp-1 max-w-[200px]" title="{{ $sm->module_title }}">
                                    {{ $sm->module_title ?? $sm->module_id }}
                                </span>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <x-admin.badge variant="secondary" size="sm" class="capitalize">
                                {{ ucfirst($sm->skill_type) }}
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-semibold text-slate-600 dark:text-neutral-300">
                                {{ $sm->sections_count ?? 0 }} Bagian
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-mono font-bold text-slate-400">
                                #{{ $sm->order_index }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-5 py-2 flex items-center justify-end gap-x-1.5">
                            <x-admin.button href="{{ route('admin.sub_modules.detail', $sm->id) }}" size="sm" color="outline-secondary" title="Detail">
                                @include('_admin._layout.icons.view_detail')
                            </x-admin.button>
                            <x-admin.button href="{{ route('admin.sub_modules.update', $sm->id) }}" size="sm" color="outline-secondary" title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </x-admin.button>
                            <form action="{{ route('admin.sub_modules.delete', $sm->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-modul ini?')">
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
                        <x-admin.table.td colspan="6" class="text-center py-8">
                            <x-admin.empty-state message="Tidak ada data sub-modul ditemukan." />
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
