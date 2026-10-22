@extends('_admin._layout.app')

@section('title', 'Manajemen Sertifikat')

@section('content')
    <x-admin.page-header :title="'Manajemen Sertifikat'" subtitle="Kelola master template per level CEFR dan rekap sertifikat resmi yang telah diterbitkan untuk siswa.">
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

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex items-center gap-2 border-b border-gray-200 dark:border-neutral-700">
        <a href="{{ route('admin.certificates.index', ['tab' => 'templates']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'templates' ? 'border-primary text-primary dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Master Template Kategori (A1 - C2)
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
        {{-- TAB 1: MASTER TEMPLATES PER CATEGORY --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($templates as $t)
                <x-admin.card fit="true" class="flex flex-col justify-between border border-gray-200/80 dark:border-neutral-700/80 shadow-xs hover:shadow-md transition-all">
                    <div>
                        {{-- Top Header --}}
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <span class="inline-flex items-center justify-center size-10 rounded-xl bg-primary text-white font-black text-sm shadow-sm" style="background-color: {{ $t->theme_color ?? '#0D3B66' }}">
                                {{ $t->cefr }}
                            </span>
                            <div class="text-right">
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">
                                    {{ $t->issued_count }} Siswa Lulus
                                </span>
                            </div>
                        </div>

                        <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight mb-1">
                            {{ $t->title }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-3 mb-4 leading-relaxed">
                            {{ $t->description ?? 'Deskripsi kompetensi belum diatur.' }}
                        </p>

                        {{-- Template Preview / Background Thumbnail --}}
                        <div class="mb-4 rounded-lg overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50 aspect-video flex items-center justify-center relative">
                            @if ($t->background_url)
                                <img src="{{ $t->background_url }}" alt="Background Template {{ $t->cefr }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-2 right-2 px-2 py-0.5 rounded bg-black/60 text-white text-[10px] font-mono">
                                    Custom Background
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-neutral-500 p-4 text-center">
                                    <svg class="size-8 mb-1 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs">Background Standar EDDY'S</span>
                                </div>
                            @endif
                        </div>

                        {{-- Signatory Info --}}
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-neutral-800/40 border border-gray-100 dark:border-neutral-700/60 text-xs mb-4">
                            <p class="font-semibold text-gray-800 dark:text-neutral-200 truncate">
                                {{ $t->instructor_name ?? 'Academic Council' }}
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-neutral-400 truncate">
                                {{ $t->instructor_title ?? 'Head of Academic Affairs' }}
                            </p>
                        </div>
                    </div>

                    {{-- Edit Action Button --}}
                    <div class="pt-3 border-t border-gray-100 dark:border-neutral-800">
                        <x-admin.button href="{{ route('admin.certificates.edit', ['cefr' => $t->cefr]) }}" size="sm" color="primary" class="w-full justify-center">
                            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Template & Background
                        </x-admin.button>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
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
                    <input type="text" name="keywords" value="{{ $keywords }}" placeholder="Cari nomor / siswa / email..."
                           class="px-3.5 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-gray-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-primary/20 w-60">
                    <x-admin.button type="submit" size="sm" color="primary">
                        Cari
                    </x-admin.button>
                </div>
            </form>

            {{-- Issued Certificates Table --}}
            <x-admin.card fit="true">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-neutral-700 bg-gray-50/75 dark:bg-neutral-800/60 font-semibold text-gray-600 dark:text-neutral-300">
                                <th class="py-3 px-4">No. Sertifikat</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4 text-center">Level</th>
                                <th class="py-3 px-4 text-center">Skor Akhir</th>
                                <th class="py-3 px-4 text-right">Tanggal Terbit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                            @forelse ($issuedList as $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/30 transition-colors">
                                    <td class="py-3 px-4 font-mono font-semibold text-primary dark:text-primary-400">
                                        {{ $item->certificate_number }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                        {{ $item->current_user_name ?? $item->cert_user_name }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-500 dark:text-neutral-400">
                                        {{ $item->user_email ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded font-black text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                            {{ $item->cefr_level }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $item->score }}%
                                    </td>
                                    <td class="py-3 px-4 text-right text-gray-500 dark:text-neutral-400">
                                        {{ \Carbon\Carbon::parse($item->issued_at)->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 dark:text-neutral-500">
                                        <p class="text-sm font-medium">Belum ada sertifikat siswa yang diterbitkan.</p>
                                        <p class="text-xs mt-1">Sertifikat akan otomatis tercatat saat siswa mengklaim kelulusan level di aplikasi english-ai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($issuedList, 'links'))
                    <div class="p-4 border-t border-gray-100 dark:border-neutral-800">
                        {{ $issuedList->links() }}
                    </div>
                @endif
            </x-admin.card>
        </div>
    @endif
@endsection
