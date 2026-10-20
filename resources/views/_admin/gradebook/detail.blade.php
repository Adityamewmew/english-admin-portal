@extends('_admin._layout.app')

@section('title', 'Detail Pekerjaan Siswa - ' . ($student->name ?? 'Siswa'))

@section('content')
    <div class="space-y-6">
        {{-- Header & Back Navigation --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.gradebook.index') }}"
                    class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white shadow-2xs">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Lembar Rekap Nilai & Pekerjaan Siswa
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Inspeksi pengerjaan modul harian dan ujian kelulusan tingkat CEFR
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.detail', $student->id) }}"
                    class="py-2 px-3 text-xs font-semibold rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200">
                    Lihat Profil Pengguna
                </a>
            </div>
        </div>

        {{-- Student Profile Hero Card --}}
        <div class="p-6 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/90 dark:border-neutral-700 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-2xl bg-gradient-to-br from-[#0D3B66] to-[#1B4965] text-white text-2xl font-bold flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->name }}</h3>
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-800 dark:bg-neutral-800 dark:text-neutral-200 border border-slate-200 dark:border-neutral-700">
                                Level {{ $student->current_cefr ?? 'A1' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">{{ $student->email }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-neutral-500 mt-0.5">
                            Terdaftar: {{ \Carbon\Carbon::parse($student->created_at)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- Summary Stats Pills --}}
                @php
                    $completedModules = collect($modules)->filter(fn($m) => $m->status === 'completed');
                    $m13Modules = $completedModules->filter(fn($m) => $m->is_exam || str_ends_with($m->module_id, '-M13'));
                    $scored = $completedModules->filter(fn($m) => !is_null($m->score) && $m->score > 0);
                    $avgScore = $scored->count() > 0 ? round($scored->avg('score')) : 0;
                @endphp
                <div class="grid grid-cols-3 gap-3 w-full md:w-auto">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-100 dark:border-neutral-700 text-center min-w-[100px]">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase">Modul Selesai</span>
                        <span class="block text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $completedModules->count() }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-100 dark:border-neutral-700 text-center min-w-[100px]">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase">Rata-rata Skor</span>
                        <span class="block text-lg font-bold {{ $avgScore >= 75 ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5">
                            {{ $avgScore > 0 ? $avgScore : '-' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-100 dark:border-neutral-700 text-center min-w-[100px]">
                        <span class="block text-[11px] font-semibold text-gray-400 uppercase">Ujian M13 Lulus</span>
                        <span class="block text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $m13Modules->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 1: Ujian Kelulusan Stage (M13 Exams) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>Ujian Kelulusan Level (Stage Graduation Exams - M13)</span>
                        <span class="px-2 py-0.5 rounded text-xs bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800">
                            55 Soal Komprehensif
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Ujian evaluasi akhir penentu kenaikan tingkat CEFR dan penerbitan sertifikat
                    </p>
                </div>
            </div>

            <x-admin.table.wrapper>
                <x-admin.table>
                    <x-admin.table.thead>
                        <tr>
                            <x-admin.table.th>Kode Ujian</x-admin.table.th>
                            <x-admin.table.th>Nama Ujian & Stage</x-admin.table.th>
                            <x-admin.table.th align="center">Passing Score</x-admin.table.th>
                            <x-admin.table.th align="center">Skor Siswa</x-admin.table.th>
                            <x-admin.table.th align="center">Status Kelulusan</x-admin.table.th>
                            <x-admin.table.th align="end">Tanggal Ujian</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @php
                            $allM13 = collect($modules)->filter(fn($m) => $m->is_exam || str_ends_with($m->module_id, '-M13'));
                        @endphp
                        @forelse($allM13 as $exam)
                            <x-admin.table.tr>
                                <x-admin.table.td>
                                    <span class="font-mono text-xs font-bold text-[#0D3B66] dark:text-[#F4B41A]">
                                        {{ $exam->module_id }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    <span class="font-bold text-sm text-gray-900 dark:text-white block">{{ $exam->title }}</span>
                                    <span class="text-xs text-gray-400">Level {{ $exam->level_id }} · CEFR {{ $exam->cefr }}</span>
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-neutral-400">{{ $exam->passing_score ?? 70 }}</span>
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    <span class="text-base font-extrabold {{ ($exam->score ?? 0) >= ($exam->passing_score ?? 70) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                                        {{ $exam->score ?? '-' }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    @if($exam->status === 'completed')
                                        @if(($exam->score ?? 0) >= ($exam->passing_score ?? 70))
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                                LULUS SERTIFIKASI
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                                REMEDIAL
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum Selesai</span>
                                    @endif
                                </x-admin.table.td>
                                <x-admin.table.td align="end">
                                    <span class="text-xs text-gray-500 dark:text-neutral-400">
                                        {{ $exam->completed_at ? \Carbon\Carbon::parse($exam->completed_at)->translatedFormat('d M Y, H:i') : '-' }}
                                    </span>
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-gray-400 italic">
                                    Siswa belum pernah mengikuti Ujian Kelulusan Stage (M13).
                                </td>
                            </tr>
                        @endforelse
                    </x-admin.table.tbody>
                </x-admin.table>
            </x-admin.table.wrapper>
        </div>

        {{-- Section 2: Pekerjaan Unit Harian (Daily Units M01-M12) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>Pekerjaan Unit Harian (Daily Units M01–M12)</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Daftar unit yang telah dikerjakan beserta status 4 submodul & nilai Final Mission
                    </p>
                </div>
            </div>

            <x-admin.table.wrapper>
                <x-admin.table>
                    <x-admin.table.thead>
                        <tr>
                            <x-admin.table.th>Kode Unit</x-admin.table.th>
                            <x-admin.table.th>Judul Unit</x-admin.table.th>
                            <x-admin.table.th align="center">Submodul Dikerjakan</x-admin.table.th>
                            <x-admin.table.th align="center">Nilai Final Mission</x-admin.table.th>
                            <x-admin.table.th align="center">Status Unit</x-admin.table.th>
                            <x-admin.table.th align="end">Waktu Selesai</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @php
                            $dailyUnits = collect($modules)->filter(fn($m) => !$m->is_exam && !str_ends_with($m->module_id, '-M13'));
                        @endphp
                        @forelse($dailyUnits as $unit)
                            @php
                                $subs = collect($unit->sub_modules ?? []);
                                $assessment = $subs->first(fn($s) => $s->skill_type === 'assessment' || $s->skill_type === 'exam');
                            @endphp
                            <x-admin.table.tr>
                                <x-admin.table.td>
                                    <span class="font-mono text-xs font-bold text-gray-700 dark:text-neutral-300">
                                        {{ $unit->module_id }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    <span class="font-bold text-sm text-gray-900 dark:text-white block">{{ $unit->title }}</span>
                                    <span class="text-xs text-gray-400">Level {{ $unit->level_id }} · CEFR {{ $unit->cefr }}</span>
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        @foreach($subs as $sub)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $sub->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-neutral-400' }}"
                                                  title="{{ $sub->title }} ({{ $sub->status }})">
                                                {{ ucfirst(substr($sub->skill_type, 0, 4)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    @if($assessment && !is_null($assessment->score))
                                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $assessment->score >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $assessment->score }}
                                        </span>
                                    @elseif(!is_null($unit->score))
                                        <span class="text-xs font-bold text-gray-800 dark:text-neutral-200">
                                            {{ $unit->score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </x-admin.table.td>
                                <x-admin.table.td align="center">
                                    @if($unit->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Dalam Proses</span>
                                    @endif
                                </x-admin.table.td>
                                <x-admin.table.td align="end">
                                    <span class="text-xs text-gray-500 dark:text-neutral-400">
                                        {{ $unit->completed_at ? \Carbon\Carbon::parse($unit->completed_at)->translatedFormat('d M Y, H:i') : '-' }}
                                    </span>
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-gray-400 italic">
                                    Belum ada pekerjaan unit harian yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </x-admin.table.tbody>
                </x-admin.table>
            </x-admin.table.wrapper>
        </div>
    </div>
@endsection
