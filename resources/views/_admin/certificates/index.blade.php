@extends('_admin._layout.app')

@section('title', 'Manajemen Sertifikat')

@section('content')
    <x-admin.page-header :title="'Manajemen Sertifikat'" subtitle="Kelola master template sertifikat per level CEFR dan rekap sertifikat siswa yang telah diterbitkan.">
        <x-admin.button href="{{ route('admin.certificates.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Sertifikat Baru
        </x-admin.button>
    </x-admin.page-header>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex items-center gap-2 border-b border-gray-200 dark:border-neutral-700">
        <a href="{{ route('admin.certificates.index', ['tab' => 'templates']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'templates' ? 'border-primary text-primary dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Daftar Template Sertifikat (CRUD)
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold">
                {{ count($templates) }}
            </span>
        </a>
        <a href="{{ route('admin.certificates.index', ['tab' => 'issued']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'issued' ? 'border-primary text-primary dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Rekap Siswa Penerima Sertifikat
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold">
                {{ $totalIssued }}
            </span>
        </a>
    </div>

    @if ($activeTab === 'templates')
        {{-- TAB 1: MASTER TEMPLATES CRUD TABLE --}}
        <x-admin.table.wrapper>
            <x-admin.table>
                <x-admin.table.thead>
                    <tr>
                        <x-admin.table.th>Level CEFR</x-admin.table.th>
                        <x-admin.table.th>Judul & Deskripsi</x-admin.table.th>
                        <x-admin.table.th>Background Template</x-admin.table.th>
                        <x-admin.table.th>Penandatangan</x-admin.table.th>
                        <x-admin.table.th class="text-center">Siswa Lulus</x-admin.table.th>
                        <x-admin.table.th align="end">Aksi</x-admin.table.th>
                    </tr>
                </x-admin.table.thead>
                <x-admin.table.tbody>
                    @forelse ($templates as $t)
                        <x-admin.table.tr>
                            {{-- CEFR Badge --}}
                            <x-admin.table.td>
                                <span class="inline-flex items-center justify-center size-9 rounded-xl font-black text-white text-xs shadow-xs" style="background-color: {{ $t->theme_color ?? '#0D3B66' }}">
                                    {{ $t->cefr }}
                                </span>
                            </x-admin.table.td>

                            {{-- Title & Description --}}
                            <x-admin.table.td>
                                <div class="max-w-md">
                                    <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                                        {{ $t->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2 mt-0.5">
                                        {{ $t->description ?? '-' }}
                                    </p>
                                </div>
                            </x-admin.table.td>

                            {{-- Background Thumbnail --}}
                            <x-admin.table.td>
                                @if ($t->background_url)
                                    <a href="{{ $t->background_url }}" target="_blank" class="inline-block group relative">
                                        <img src="{{ $t->background_url }}" alt="Background {{ $t->cefr }}" class="h-10 w-16 object-cover rounded border border-gray-200 dark:border-neutral-700 shadow-2xs group-hover:scale-105 transition-transform">
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-400 dark:text-neutral-500 italic">
                                        Default Template
                                    </span>
                                @endif
                            </x-admin.table.td>

                            {{-- Instructor --}}
                            <x-admin.table.td>
                                <div class="text-xs">
                                    <p class="font-semibold text-gray-800 dark:text-neutral-200">
                                        {{ $t->instructor_name ?? 'Academic Council' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-neutral-400">
                                        {{ $t->instructor_title ?? 'Head of Academic Affairs' }}
                                    </p>
                                </div>
                            </x-admin.table.td>

                            {{-- Total Issued Count --}}
                            <x-admin.table.td class="text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">
                                    {{ $t->issued_count }}
                                </span>
                            </x-admin.table.td>

                            {{-- Actions --}}
                            <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end gap-x-2">
                                <a href="{{ route('admin.certificates.edit', ['cefr' => $t->cefr]) }}"
                                   class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400" title="Edit">
                                    @include('_admin._layout.icons.pencil')
                                    Edit
                                </a>

                                <form action="{{ route('admin.certificates.delete', ['cefr' => $t->cefr]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template sertifikat level {{ $t->cefr }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 cursor-pointer" title="Hapus">
                                        @include('_admin._layout.icons.trash')
                                        Hapus
                                    </button>
                                </form>
                            </x-admin.table.td>
                        </x-admin.table.tr>
                    @empty
                        <x-admin.table.tr>
                            <x-admin.table.td colspan="6" class="text-center py-10">
                                <x-admin.empty-state message="Belum ada template sertifikat yang dibuat. Klik 'Tambah Sertifikat Baru' di atas untuk mulai membuat template." />
                            </x-admin.table.td>
                        </x-admin.table.tr>
                    @endforelse
                </x-admin.table.tbody>
            </x-admin.table>
        </x-admin.table.wrapper>
    @else
        {{-- TAB 2: REKAP SISWA PENERIMA SERTIFIKAT --}}
        <div class="space-y-4">
            {{-- Filter & Search Form --}}
            <form action="{{ route('admin.certificates.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
                <input type="hidden" name="tab" value="issued">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Level CEFR:</span>
                    @foreach (['all', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $c)
                        <a href="{{ route('admin.certificates.index', ['tab' => 'issued', 'cefr' => $c, 'keywords' => $keywords]) }}"
                           class="px-3 py-1 text-xs rounded-full font-medium transition-colors {{ $cefr === $c ? 'bg-primary text-white font-bold' : 'bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 hover:bg-gray-200' }}">
                            {{ $c === 'all' ? 'Semua Level' : $c }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari nomor / siswa / email..." size="sm" />
                    <x-admin.button type="submit" size="sm" color="primary">
                        Cari
                    </x-admin.button>
                </div>
            </form>

            {{-- Issued Certificates Table --}}
            <x-admin.table.wrapper>
                <x-admin.table>
                    <x-admin.table.thead>
                        <tr>
                            <x-admin.table.th>No. Sertifikat</x-admin.table.th>
                            <x-admin.table.th>Nama Siswa</x-admin.table.th>
                            <x-admin.table.th>Email</x-admin.table.th>
                            <x-admin.table.th class="text-center">Level</x-admin.table.th>
                            <x-admin.table.th class="text-center">Skor Akhir</x-admin.table.th>
                            <x-admin.table.th align="end">Tanggal Terbit</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @forelse ($issuedList as $item)
                            <x-admin.table.tr>
                                <x-admin.table.td class="font-mono font-semibold text-primary dark:text-primary-400">
                                    {{ $item->certificate_number }}
                                </x-admin.table.td>
                                <x-admin.table.td class="font-semibold text-gray-900 dark:text-white">
                                    {{ $item->current_user_name ?? $item->cert_user_name }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-gray-500 dark:text-neutral-400">
                                    {{ $item->user_email ?? '-' }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded font-black text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                        {{ $item->cefr_level }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $item->score }}%
                                </x-admin.table.td>
                                <x-admin.table.td align="end" class="text-gray-500 dark:text-neutral-400">
                                    {{ \Carbon\Carbon::parse($item->issued_at)->format('d M Y, H:i') }}
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <x-admin.table.tr>
                                <x-admin.table.td colspan="6" class="text-center py-10">
                                    <x-admin.empty-state message="Belum ada sertifikat siswa yang diterbitkan." />
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @endforelse
                    </x-admin.table.tbody>
                </x-admin.table>
            </x-admin.table.wrapper>

            @if (method_exists($issuedList, 'links'))
                <div class="mt-4">
                    {{ $issuedList->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection
