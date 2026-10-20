@extends('_admin._layout.app')

@section('title', 'Rekap Nilai Siswa')

@section('content')
    <x-admin.page-header title="Rekap Nilai Siswa (Gradebook)" subtitle="Pantau kemajuan belajar, nilai ujian kelulusan M13, dan tugas unit harian siswa">
    </x-admin.page-header>

    {{-- 1. Metric Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-400">Total Siswa</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($summary['total_students'] ?? 0) }}</span>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Terdaftar</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-400">Modul Diselesaikan</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($summary['total_completed_modules'] ?? 0) }}</span>
                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Submission</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-400">Rata-rata Nilai</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $summary['overall_avg_score'] ?? 0 }}<span class="text-sm font-normal text-gray-500">/100</span></span>
                <span class="text-xs font-semibold {{ ($summary['overall_avg_score'] ?? 0) >= 75 ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ ($summary['overall_avg_score'] ?? 0) >= 75 ? 'Kompeten' : 'Evaluasi' }}
                </span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-400">Ujian M13 Lulus</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($summary['total_passed_exams'] ?? 0) }}</span>
                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">Tersertifikasi</span>
            </div>
        </div>
    </div>

    {{-- 2. Filter Bar --}}
    <div class="mb-6">
        <form action="{{ route('admin.gradebook.index') }}" method="GET" navigate-form
            class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-72">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari nama atau email siswa..." size="sm" />
            </div>

            <div class="w-full sm:w-48">
                @php
                    $levelOptions = [
                        'all' => 'Semua Level CEFR',
                        'A1' => 'Level A1 (Starter)',
                        'A2' => 'Level A2 (Elementary)',
                        'B1' => 'Level B1 (Intermediate)',
                        'B2' => 'Level B2 (Upper-Int)',
                        'C1' => 'Level C1 (Advanced)',
                        'C2' => 'Level C2 (Mastery)',
                    ];
                @endphp
                <x-admin.select :label="null" name="level" :options="$levelOptions" :value="$level ?? 'all'" size="sm" class="cursor-pointer" />
            </div>

            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary">
                    @include('_admin._layout.icons.search')
                    Cari
                </x-admin.button>
                @if (!empty($keywords) || ($level ?? 'all') !== 'all')
                    <x-admin.button href="{{ route('admin.gradebook.index') }}" size="sm" color="outline-secondary">
                        @include('_admin._layout.icons.reset')
                        Reset
                    </x-admin.button>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. Gradebook Table --}}
    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Siswa</x-admin.table.th>
                    <x-admin.table.th>Level CEFR</x-admin.table.th>
                    <x-admin.table.th align="center">Modul Selesai</x-admin.table.th>
                    <x-admin.table.th align="center">Rata-rata Nilai</x-admin.table.th>
                    <x-admin.table.th align="center">Ujian M13 Lulus</x-admin.table.th>
                    <x-admin.table.th align="center">Final Mission Unit</x-admin.table.th>
                    <x-admin.table.th align="end">Aksi</x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $s)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <div class="flex items-center gap-x-3">
                                <span class="size-9 rounded-xl bg-gradient-to-br from-[#0D3B66] to-[#1B4965] text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <span class="block text-sm font-bold text-gray-900 dark:text-white truncate">
                                        {{ $s->name }}
                                    </span>
                                    <span class="block text-xs text-gray-500 dark:text-neutral-400 truncate">
                                        {{ $s->email }}
                                    </span>
                                </div>
                            </div>
                        </x-admin.table.td>

                        <x-admin.table.td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-800 dark:bg-neutral-800 dark:text-neutral-200 border border-slate-200 dark:border-neutral-700">
                                {{ $s->current_cefr ?? 'A1' }}
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                {{ (int) $s->completed_modules_count }}
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            @if ((int) $s->avg_score > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $s->avg_score >= 75 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : ($s->avg_score >= 60 ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800') }}">
                                    {{ (int) $s->avg_score }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ada nilai</span>
                            @endif
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            <span class="inline-flex items-center gap-1 text-xs font-bold {{ (int) $s->passed_exams_count > 0 ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-gray-400' }}">
                                {{ (int) $s->passed_exams_count }} Stage
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-700 dark:text-neutral-300">
                                {{ (int) $s->completed_assessments_count }} Unit
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="end">
                            <a navigate href="{{ route('admin.gradebook.detail', $s->id) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#0D3B66] text-white hover:bg-[#1B4965] transition-all shadow-2xs">
                                <span>Cek Pekerjaan</span>
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-neutral-500">
                            Tidak ada data siswa ditemukan untuk kriteria filter ini.
                        </td>
                    </tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>

        @if(method_exists($data, 'hasPages') && $data->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $data->links() }}
            </div>
        @endif
    </x-admin.table.wrapper>
@endsection
