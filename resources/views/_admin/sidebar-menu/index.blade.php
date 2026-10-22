@extends('_admin._layout.app')

@section('title', 'Manajemen Menu Sidebar')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Kelola menu sidebar dan hak aksesnya">
        <x-admin.button href="{{ route('admin.sidebar_menu.role_access', 1) }}" size="sm" color="outline-secondary">
            <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Akses per Role
        </x-admin.button>
        <x-admin.button href="{{ route('admin.sidebar_menu.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Menu
        </x-admin.button>
    </x-admin.page-header>

    <div class="mb-6">
        <x-admin.card variant="white" padding="p-4 sm:p-5">
            <form action="{{ route('admin.sidebar_menu.index') }}" method="GET" navigate-form
                class="flex flex-col sm:flex-row items-center gap-3">
                <div class="w-full sm:w-64">
                    <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari label menu..." size="sm" />
                </div>
                <div class="w-full sm:w-48">
                    @php
                        $groupOptions = ['' => 'Semua Group'] + collect($groups)->pluck('label', 'key')->toArray();
                    @endphp
                    <x-admin.select :label="null" name="group" :options="$groupOptions" :value="$group ?? ''" size="sm" class="cursor-pointer" />
                </div>
                <div class="flex items-center gap-2">
                    <x-admin.button type="submit" size="sm" color="primary">
                        @include('_admin._layout.icons.search')
                        Cari
                    </x-admin.button>
                    @if (!empty($keywords) || !empty($group))
                        <x-admin.button href="{{ route('admin.sidebar_menu.index') }}" size="sm" color="outline-secondary">
                            @include('_admin._layout.icons.reset')
                            Reset
                        </x-admin.button>
                    @endif
                </div>
            </form>
        </x-admin.card>
    </div>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Label Menu</x-admin.table.th>
                    <x-admin.table.th>Group</x-admin.table.th>
                    <x-admin.table.th>Parent</x-admin.table.th>
                    <x-admin.table.th>Route</x-admin.table.th>
                    <x-admin.table.th>Urutan</x-admin.table.th>
                    <x-admin.table.th>Akses</x-admin.table.th>
                    <x-admin.table.th>Status</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $d)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <span class="text-sm font-extrabold text-primary dark:text-neutral-200">
                                {{ $d->label }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @php
                                $groupObj = collect($groups)->firstWhere('key', $d->group);
                            @endphp
                            <x-admin.badge variant="primary" size="sm">
                                {{ $groupObj->label ?? ucfirst($d->group) }}
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs text-slate-500 dark:text-neutral-400">
                                {{ $d->parent_label ?? '-' }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-mono text-slate-500 dark:text-neutral-400">
                                {{ $d->route_name ?? '-' }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-xs font-mono font-bold text-slate-500">{{ $d->sort_order }}</span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <x-admin.badge variant="{{ $d->access_count > 0 ? 'success' : 'neutral' }}" size="sm">
                                {{ $d->access_count }} role
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if ($d->is_active)
                                <x-admin.badge variant="success" size="sm">
                                    Aktif
                                </x-admin.badge>
                            @else
                                <x-admin.badge variant="danger" size="sm">
                                    Nonaktif
                                </x-admin.badge>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-5 py-2 flex items-center justify-end gap-x-1.5">
                            <x-admin.button href="{{ route('admin.sidebar_menu.access', $d->id) }}" size="sm" color="outline-secondary" title="Kelola Akses">
                                @include('_admin._layout.icons.sidebar.change-password')
                            </x-admin.button>
                            <x-admin.button href="{{ route('admin.sidebar_menu.update', $d->id) }}" size="sm" color="outline-secondary" title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </x-admin.button>
                            <x-admin.button type="button" size="sm" color="outline-danger" title="Hapus" data-hs-overlay="#delete-modal"
                                onclick="setDeleteData('{{ $d->id }}', '{{ $d->label }}')">
                                @include('_admin._layout.icons.trash')
                            </x-admin.button>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="8" class="text-center py-8">
                            <x-admin.empty-state message="Belum ada menu sidebar ditemukan." />
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>
        @if (count($data) > 0 && $data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </x-admin.table.wrapper>

    {{-- Delete Modal --}}
    <x-admin.modal id="delete-modal" title="Konfirmasi Hapus Menu Sidebar">
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600 dark:text-neutral-400">
                Apakah Anda yakin ingin menghapus menu <strong id="delete-name" class="text-primary font-bold"></strong>?
            </p>
            <div class="mt-5 flex justify-end gap-x-2">
                <x-admin.button type="button" color="outline-secondary" size="sm" data-hs-overlay="#delete-modal">Batal</x-admin.button>
                <x-admin.button type="submit" color="danger" size="sm">Hapus</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
@endsection

@push('scripts')
    <script>
        function setDeleteData(id, name) {
            document.getElementById('delete-name').textContent = name;
            document.getElementById('delete-form').action = `/admin/sidebar-menu/delete/${id}`;
        }
    </script>
@endpush
