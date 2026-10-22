@extends('_admin._layout.app')

@section('title', 'Rekap Nilai Siswa')

@section('content')
    <x-admin.page-header title="Rekap Nilai Siswa (Gradebook)" subtitle="Pantau kemajuan belajar, nilai ujian kelulusan M13, dan tugas unit harian siswa" />

    {{-- 1. Metric Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.card variant="white" padding="p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-primary dark:text-white">{{ number_format($summary['total_students'] ?? 0) }}</span>
                <x-admin.badge variant="neutral" size="sm">Terdaftar</x-admin.badge>
            </div>
        </x-admin.card>

        <x-admin.card variant="white" padding="p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Modul Diselesaikan</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-primary dark:text-white">{{ number_format($summary['total_completed_modules'] ?? 0) }}</span>
                <x-admin.badge variant="success" size="sm">Submission</x-admin.badge>
            </div>
        </x-admin.card>

        <x-admin.card variant="white" padding="p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Nilai</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-primary dark:text-white">{{ $summary['overall_avg_score'] ?? 0 }}<span class="text-xs font-normal text-slate-400">/100</span></span>
                <x-admin.badge variant="{{ ($summary['overall_avg_score'] ?? 0) >= 75 ? 'success' : 'warning' }}" size="sm">
                    {{ ($summary['overall_avg_score'] ?? 0) >= 75 ? 'Kompeten' : 'Evaluasi' }}
                </x-admin.badge>
            </div>
        </x-admin.card>

        <x-admin.card variant="white" padding="p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ujian M13 Lulus</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-secondary dark:text-secondary-light">{{ number_format($summary['total_passed_exams'] ?? 0) }}</span>
                <x-admin.badge variant="achievement" size="sm">Tersertifikasi</x-admin.badge>
            </div>
        </x-admin.card>
    </div>

    {{-- 2. Filter Bar --}}
    <x-admin.card variant="white" padding="p-4 sm:p-5" class="mb-6">
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
    </x-admin.card>

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
                                <span class="size-9 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300 text-primary text-xs font-black flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <span class="block text-sm font-extrabold text-primary dark:text-white truncate">
                                        {{ $s->name }}
                                    </span>
                                    <span class="block text-xs text-slate-500 dark:text-neutral-400 font-mono truncate">
                                        {{ $s->email }}
                                    </span>
                                </div>
                            </div>
                        </x-admin.table.td>

                        <x-admin.table.td>
                            <x-admin.badge :cefr="$s->current_cefr ?? 'A1'" size="sm">
                                {{ $s->current_cefr ?? 'A1' }}
                            </x-admin.badge>
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            <span class="text-sm font-extrabold text-slate-800 dark:text-neutral-200">
                                {{ (int) $s->completed_modules_count }}
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            @if ((int) $s->avg_score > 0)
                                <x-admin.badge variant="{{ $s->avg_score >= 75 ? 'success' : ($s->avg_score >= 60 ? 'warning' : 'danger') }}" size="sm">
                                    {{ (int) $s->avg_score }}
                                </x-admin.badge>
                            @else
                                <span class="text-xs text-slate-400 italic">Belum ada nilai</span>
                            @endif
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            @if ((int) $s->passed_exams_count > 0)
                                <x-admin.badge variant="achievement" size="sm">
                                    {{ (int) $s->passed_exams_count }} Stage
                                </x-admin.badge>
                            @else
                                <span class="text-xs text-slate-400">0 Stage</span>
                            @endif
                        </x-admin.table.td>

                        <x-admin.table.td align="center">
                            <span class="text-xs font-semibold text-slate-700 dark:text-neutral-300">
                                {{ (int) $s->completed_assessments_count }} Unit
                            </span>
                        </x-admin.table.td>

                        <x-admin.table.td align="end">
                            <x-admin.button href="{{ route('admin.gradebook.detail', $s->id) }}" size="sm" color="primary">
                                <span>Cek Pekerjaan</span>
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </x-admin.button>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <x-admin.table.tr>
                        <x-admin.table.td colspan="7" class="text-center py-8">
                            <x-admin.empty-state message="Tidak ada data siswa ditemukan untuk kriteria filter ini." />
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>

        @if(method_exists($data, 'hasPages') && $data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-neutral-700">
                {{ $data->links() }}
            </div>
        @endif
    </x-admin.table.wrapper>
@endsection
