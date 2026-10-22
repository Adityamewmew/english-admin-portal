@extends('_admin._layout.app')

@section('title', 'Pengguna Aplikasi')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Kelola data akun siswa dan administrator sistem">
        <x-admin.button href="{{ route('admin.users.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Pengguna
        </x-admin.button>
    </x-admin.page-header>

    <div class="mb-6">
        <x-admin.card variant="white" padding="p-4 sm:p-5">
            <form action="{{ route('admin.users.index') }}" method="GET" navigate-form
                class="flex flex-col sm:flex-row items-center gap-3">
                <div class="w-full sm:w-64">
                    <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari nama atau email..." size="sm" />
                </div>
                <div class="w-full sm:w-48">
                    @php
                        $accessTypeOptions = ['all' => 'Semua Hak Akses'] + UserConst::getAppAccessTypes();
                    @endphp
                    <x-admin.select :label="null" name="access_type" :options="$accessTypeOptions" :value="$access_type ?? 'all'" size="sm"
                        class="cursor-pointer" />
                </div>
                <div class="flex items-center gap-2">
                    <x-admin.button type="submit" size="sm" color="primary">
                        @include('_admin._layout.icons.search')
                        Cari
                    </x-admin.button>
                    @if (!empty($keywords) || ($access_type ?? 'all') !== 'all')
                        <x-admin.button href="{{ route('admin.users.index') }}" size="sm" color="outline-secondary">
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
                    <x-admin.table.th>Nama & Email</x-admin.table.th>
                    <x-admin.table.th>Hak Akses</x-admin.table.th>
                    <x-admin.table.th>Level CEFR</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $d)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <div class="flex items-center gap-x-3">
                                <span
                                    class="inline-flex items-center justify-center size-9 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300 text-primary font-black text-xs shadow-2xs">
                                    {{ strtoupper(substr($d->name, 0, 1)) }}
                                </span>
                                <div class="grow">
                                    <span class="block text-sm font-extrabold text-primary dark:text-neutral-200">{{ $d->name }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-neutral-400 font-mono">{{ $d->email }}</span>
                                </div>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if ((int) $d->access_type === UserConst::SUPERADMIN)
                                <x-admin.badge variant="primary" size="sm">
                                    Administrator
                                </x-admin.badge>
                            @else
                                <x-admin.badge variant="secondary" size="sm">
                                    Siswa
                                </x-admin.badge>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <x-admin.badge :cefr="$d->current_cefr ?? 'A1'" size="sm">
                                {{ $d->current_cefr ?? 'A1' }}
                            </x-admin.badge>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-5 py-2 flex items-center justify-end gap-x-1.5">
                            <x-admin.button href="{{ route('admin.users.detail', $d->id) }}" size="sm" color="outline-secondary" title="Lihat Detail">
                                @include('_admin._layout.icons.view_detail')
                            </x-admin.button>
                            <x-admin.button href="{{ route('admin.users.update', $d->id) }}" size="sm" color="outline-secondary" title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </x-admin.button>
                            <x-admin.button type="button" size="sm" color="outline-secondary"
                                title="Reset Password" data-hs-overlay="#reset-modal"
                                onclick="setResetData('{{ $d->id }}', '{{ $d->name }}')">
                                @include('_admin._layout.icons.sidebar.change-password')
                            </x-admin.button>
                            <x-admin.button type="button" size="sm" color="outline-danger"
                                title="Hapus" data-hs-overlay="#delete-modal"
                                onclick="setDeleteData('{{ $d->id }}', '{{ $d->name }}')">
                                @include('_admin._layout.icons.trash')
                            </x-admin.button>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="4" class="text-center py-8">
                            <x-admin.empty-state message="Tidak ada data pengguna ditemukan." />
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

    {{-- Reset Password Modal --}}
    <x-admin.modal id="reset-modal" title="Konfirmasi Reset Password">
        <form id="reset-form" method="POST" action="">
            @csrf
            <p class="text-sm text-slate-600 dark:text-neutral-400">
                Apakah Anda yakin ingin mereset password untuk <span id="reset-user-name" class="font-bold text-primary dark:text-white"></span>? Password akan dikembalikan ke <span class="font-mono font-bold text-secondary">password123</span>.
            </p>
            <div class="mt-5 flex justify-end gap-x-2">
                <x-admin.button type="button" color="outline-secondary" size="sm" data-hs-overlay="#reset-modal">Batal</x-admin.button>
                <x-admin.button type="submit" color="secondary" size="sm">Reset Password</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Delete Modal --}}
    <x-admin.modal id="delete-modal" title="Konfirmasi Hapus Pengguna">
        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600 dark:text-neutral-400">
                Apakah Anda yakin ingin menghapus data pengguna <span id="delete-user-name" class="font-bold text-primary dark:text-white"></span>? Data akan dipindahkan ke tempat sampah.
            </p>
            <div class="mt-5 flex justify-end gap-x-2">
                <x-admin.button type="button" color="outline-secondary" size="sm" data-hs-overlay="#delete-modal">Batal</x-admin.button>
                <x-admin.button type="submit" color="danger" size="sm">Hapus Data</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    @push('scripts')
        <script>
            function setResetData(id, name) {
                document.getElementById('reset-user-name').textContent = name;
                document.getElementById('reset-form').action = `{{ url('admin/users/reset-password') }}/${id}`;
            }

            function setDeleteData(id, name) {
                document.getElementById('delete-user-name').textContent = name;
                document.getElementById('delete-form').action = `{{ url('admin/users/delete') }}/${id}`;
            }
        </script>
    @endpush
@endsection
