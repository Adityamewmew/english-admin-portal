@extends('_admin._layout.app')

@section('title', 'Pengguna Aplikasi')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Kelola data siswa dan administrator sistem">
        <x-admin.button href="{{ route('admin.users.add') }}" class="font-bold">
            @include('_admin._layout.icons.add')
            Tambah Pengguna
        </x-admin.button>
    </x-admin.page-header>

    <div class="mb-6">
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
                                    class="inline-flex items-center justify-center size-9.5 rounded-full bg-blue-50 border border-blue-200 text-blue-600 font-bold dark:bg-neutral-800 dark:border-neutral-700 dark:text-blue-400">
                                    {{ strtoupper(substr($d->name, 0, 1)) }}
                                </span>
                                <div class="grow">
                                    <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $d->name }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-neutral-400">{{ $d->email }}</span>
                                </div>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if ((int) $d->access_type === UserConst::SUPERADMIN)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Siswa
                                </span>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                {{ $d->current_cefr ?? 'A1' }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end gap-x-1.5">
                            <a navigate
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                href="{{ route('admin.users.detail', $d->id) }}" title="Lihat Detail">
                                @include('_admin._layout.icons.view_detail')
                            </a>
                            <a navigate
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                href="{{ route('admin.users.update', $d->id) }}" title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </a>
                            <button type="button"
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-400 cursor-pointer"
                                title="Reset Password" data-hs-overlay="#reset-modal"
                                onclick="setResetData('{{ $d->id }}', '{{ $d->name }}')">
                                @include('_admin._layout.icons.sidebar.change-password')
                            </button>
                            <button type="button"
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-400 cursor-pointer"
                                title="Hapus" data-hs-overlay="#delete-modal"
                                onclick="setDeleteData('{{ $d->id }}', '{{ $d->name }}')">
                                @include('_admin._layout.icons.trash')
                            </button>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="4" class="text-center py-8">
                            <x-admin.empty-state text="Tidak ada data pengguna ditemukan" />
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
            <p class="text-sm text-gray-600 dark:text-neutral-400">
                Apakah Anda yakin ingin mereset password untuk <span id="reset-user-name" class="font-bold text-gray-900 dark:text-white"></span>? Password akan dikembalikan ke <span class="font-mono font-bold text-blue-600">password123</span>.
            </p>
            <div class="mt-5 flex justify-end gap-x-2">
                <button type="button" class="py-2 px-3 text-sm rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-800 dark:text-white" data-hs-overlay="#reset-modal">Batal</button>
                <button type="submit" class="py-2 px-3 text-sm rounded-lg bg-amber-600 text-white font-medium hover:bg-amber-700">Reset Password</button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Delete Modal --}}
    <x-admin.modal id="delete-modal" title="Konfirmasi Hapus Pengguna">
        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <p class="text-sm text-gray-600 dark:text-neutral-400">
                Apakah Anda yakin ingin menghapus data pengguna <span id="delete-user-name" class="font-bold text-gray-900 dark:text-white"></span>? Data akan dipindahkan ke tempat sampah (soft delete).
            </p>
            <div class="mt-5 flex justify-end gap-x-2">
                <button type="button" class="py-2 px-3 text-sm rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-800 dark:text-white" data-hs-overlay="#delete-modal">Batal</button>
                <button type="submit" class="py-2 px-3 text-sm rounded-lg bg-rose-600 text-white font-medium hover:bg-rose-700">Hapus Data</button>
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
