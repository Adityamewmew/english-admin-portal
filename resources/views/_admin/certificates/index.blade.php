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
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 border-b-2 border-b-emerald-300 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 border-b-2 border-b-rose-300 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex items-center gap-2 border-b border-slate-200 dark:border-neutral-700">
        <a href="{{ route('admin.certificates.index', ['tab' => 'templates']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $activeTab === 'templates' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Daftar Template Sertifikat
            <x-admin.badge variant="{{ $activeTab === 'templates' ? 'primary' : 'neutral' }}" size="sm">
                {{ count($templates) }}
            </x-admin.badge>
        </a>
        <a href="{{ route('admin.certificates.index', ['tab' => 'issued']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $activeTab === 'issued' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Rekap Siswa Penerima Sertifikat
            <x-admin.badge variant="{{ $activeTab === 'issued' ? 'primary' : 'neutral' }}" size="sm">
                {{ $totalIssued }}
            </x-admin.badge>
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
                                <x-admin.badge :cefr="$t->cefr" size="lg">
                                    {{ $t->cefr }}
                                </x-admin.badge>
                            </x-admin.table.td>

                            {{-- Title & Description --}}
                            <x-admin.table.td>
                                <div class="max-w-md">
                                    <p class="font-extrabold text-primary dark:text-white text-xs sm:text-sm">
                                        {{ $t->title }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-neutral-400 line-clamp-2 mt-0.5">
                                        {{ $t->description ?? '-' }}
                                    </p>
                                </div>
                            </x-admin.table.td>

                            {{-- Background Thumbnail --}}
                            <x-admin.table.td>
                                @if (!empty($t->has_background) || !empty($t->background_url))
                                    <a href="{{ route('admin.certificates.preview', ['cefr' => $t->cefr]) }}" class="inline-block group relative" title="Lihat Pratinjau Sertifikat">
                                        <img src="{{ route('admin.certificates.image', ['cefr' => $t->cefr]) }}" alt="Background {{ $t->cefr }}" loading="lazy" class="h-10 w-16 object-cover rounded-lg border border-slate-200 border-b-2 border-b-slate-300 shadow-2xs group-hover:scale-105 transition-transform">
                                    </a>
                                @else
                                    <x-admin.badge variant="neutral" size="sm">
                                        Default Template
                                    </x-admin.badge>
                                @endif
                            </x-admin.table.td>

                            {{-- Instructor --}}
                            <x-admin.table.td>
                                <div class="text-xs">
                                    <p class="font-bold text-slate-800 dark:text-neutral-200">
                                        {{ $t->instructor_name ?? 'Academic Council' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-neutral-400">
                                        {{ $t->instructor_title ?? 'Head of Academic Affairs' }}
                                    </p>
                                </div>
                            </x-admin.table.td>

                            {{-- Total Issued Count (Gamification moment: accent pill) --}}
                            <x-admin.table.td class="text-center">
                                <x-admin.badge variant="achievement" size="sm">
                                    {{ $t->issued_count }} Lulus
                                </x-admin.badge>
                            </x-admin.table.td>

                            {{-- Actions --}}
                            <x-admin.table.td innerClass="px-5 py-2 flex items-center justify-end gap-x-2">
                                <x-admin.button href="{{ route('admin.certificates.preview', ['cefr' => $t->cefr]) }}" size="sm" color="outline-secondary" title="Lihat Pratinjau">
                                    @include('_admin._layout.icons.eye')
                                    Lihat
                                </x-admin.button>

                                <x-admin.button href="{{ route('admin.certificates.edit', ['cefr' => $t->cefr]) }}" size="sm" color="outline-secondary" title="Edit">
                                    @include('_admin._layout.icons.pencil')
                                    Edit
                                </x-admin.button>

                                <form action="{{ route('admin.certificates.delete', ['cefr' => $t->cefr]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template sertifikat level {{ $t->cefr }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="submit" size="sm" color="outline-danger" title="Hapus">
                                        @include('_admin._layout.icons.trash')
                                        Hapus
                                    </x-admin.button>
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
            <x-admin.card variant="white" padding="p-4 sm:p-5">
                {{-- Filter & Search Form --}}
                <form action="{{ route('admin.certificates.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3">
                    <input type="hidden" name="tab" value="issued">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-500 dark:text-neutral-400 mr-1">Level CEFR:</span>
                        @foreach (['all', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $c)
                            <a href="{{ route('admin.certificates.index', ['tab' => 'issued', 'cefr' => $c, 'keywords' => $keywords]) }}"
                               class="px-2.5 py-1 text-xs rounded-xl font-bold transition-all {{ $cefr === $c ? 'bg-primary text-white border-b-2 border-primary-dark shadow-xs' : 'bg-surface text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                                {{ $c === 'all' ? 'Semua' : $c }}
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
            </x-admin.card>

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
                            <x-admin.table.th class="text-center">Tanggal Terbit</x-admin.table.th>
                            <x-admin.table.th align="end">Aksi</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @forelse ($issuedList as $item)
                            <x-admin.table.tr>
                                <x-admin.table.td class="font-mono font-bold text-primary dark:text-primary-400 text-xs">
                                    {{ $item->certificate_number }}
                                </x-admin.table.td>
                                <x-admin.table.td class="font-extrabold text-slate-900 dark:text-white">
                                    {{ $item->current_user_name ?? $item->cert_user_name }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-slate-500 dark:text-neutral-400 font-mono text-xs">
                                    {{ $item->user_email ?? '-' }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <x-admin.badge :cefr="$item->cefr_level" size="sm">
                                        {{ $item->cefr_level }}
                                    </x-admin.badge>
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <x-admin.badge variant="success" size="sm">
                                        {{ $item->score }}%
                                    </x-admin.badge>
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center text-slate-500 dark:text-neutral-400 text-xs">
                                    {{ \Carbon\Carbon::parse($item->issued_at)->format('d M Y, H:i') }}
                                </x-admin.table.td>
                                <x-admin.table.td innerClass="px-5 py-2 flex items-center justify-end">
                                    <x-admin.button href="{{ route('admin.certificates.preview', [
                                        'cefr' => $item->cefr_level,
                                        'student' => $item->current_user_name ?? $item->cert_user_name,
                                        'number' => $item->certificate_number,
                                        'date' => \Carbon\Carbon::parse($item->issued_at)->translatedFormat('d F Y'),
                                        'score' => $item->score,
                                    ]) }}" size="sm" color="outline-secondary" title="Lihat Sertifikat Siswa">
                                        @include('_admin._layout.icons.eye')
                                        Lihat
                                    </x-admin.button>
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <x-admin.table.tr>
                                <x-admin.table.td colspan="7" class="text-center py-10">
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
