@extends('_admin._layout.app')

@section('title', 'Detail Pekerjaan Siswa - ' . ($student->name ?? 'Siswa'))

@section('content')
    <div class="space-y-6">
        {{-- Header & Back Navigation --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.gradebook.index') }}"
                    class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                    title="Kembali ke Rekap Nilai">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-heading font-extrabold text-xl text-primary dark:text-white tracking-tight">
                        Lembar Rekap Nilai & Pekerjaan Siswa
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Inspeksi pengerjaan modul harian dan ujian kelulusan tingkat CEFR
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-admin.button href="{{ route('admin.users.detail', $student->id) }}" variant="outline" size="sm" as="a">
                    Lihat Profil Pengguna
                </x-admin.button>
            </div>
        </div>

        {{-- Student Profile Hero Card --}}
        <x-admin.card variant="white">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-2xl bg-primary text-accent border-b-4 border-slate-900 font-heading font-extrabold text-2xl flex items-center justify-center shadow-sm shrink-0">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-heading font-extrabold text-xl text-primary dark:text-white">{{ $student->name }}</h3>
                            <x-admin.badge type="cefr" :value="$student->current_cefr ?? 'A1'" />
                        </div>
                        <p class="text-xs font-medium text-slate-500 dark:text-neutral-400 mt-1">{{ $student->email }}</p>
                        <p class="text-[11px] text-slate-400 dark:text-neutral-500 mt-0.5">
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
                    <div class="p-3.5 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 text-center min-w-[110px] card-tactile">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Modul Selesai</span>
                        <span class="block text-xl font-heading font-extrabold text-primary dark:text-white mt-1">{{ $completedModules->count() }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 text-center min-w-[110px] card-tactile">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rata-rata Skor</span>
                        <span class="block text-xl font-heading font-extrabold {{ $avgScore >= 75 ? 'text-emerald-600' : 'text-secondary' }} mt-1">
                            {{ $avgScore > 0 ? $avgScore : '-' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 text-center min-w-[110px] card-tactile">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ujian M13 Lulus</span>
                        <span class="block text-xl font-heading font-extrabold text-secondary mt-1">{{ $m13Modules->count() }}</span>
                    </div>
                </div>
            </div>
        </x-admin.card>

        {{-- Section 1: Ujian Kelulusan Stage (M13 Exams) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-primary dark:text-white flex items-center gap-2">
                        <span>Ujian Kelulusan Level (Stage Graduation Exams - M13)</span>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs bg-surface text-secondary dark:text-accent font-extrabold border border-secondary/30">
                            55 Soal Komprehensif
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Ujian evaluasi akhir penentu kenaikan tingkat CEFR dan penerbitan sertifikat
                    </p>
                </div>
            </div>

            <x-admin.table.wrapper>
                <table class="min-w-full divide-y divide-slate-200 dark:divide-neutral-700">
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
                    <tbody class="divide-y divide-slate-200 dark:divide-neutral-700">
                        @php
                            $allM13 = collect($modules)->filter(fn($m) => $m->is_exam || str_ends_with($m->module_id, '-M13'));
                        @endphp
                        @forelse($allM13 as $exam)
                            <x-admin.table.tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold text-primary dark:text-accent">
                                        {{ $exam->module_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-heading font-bold text-sm text-primary dark:text-white block">{{ $exam->title }}</span>
                                    <span class="text-xs text-slate-400">Level {{ $exam->level_id }} · CEFR {{ $exam->cefr }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-bold text-slate-600 dark:text-neutral-400">{{ $exam->passing_score ?? 70 }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-base font-heading font-extrabold {{ ($exam->score ?? 0) >= ($exam->passing_score ?? 70) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                        {{ $exam->score ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
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
                                        <span class="text-xs text-slate-400 italic">Belum Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-end whitespace-nowrap">
                                    <span class="text-xs text-slate-500 dark:text-neutral-400">
                                        {{ $exam->completed_at ? \Carbon\Carbon::parse($exam->completed_at)->translatedFormat('d M Y, H:i') : '-' }}
                                    </span>
                                </td>
                            </x-admin.table.tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 italic">
                                    Siswa belum pernah mengikuti Ujian Kelulusan Stage (M13).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-admin.table.wrapper>
        </div>

        {{-- Section 2: Pekerjaan Unit Harian (Daily Units M01-M12) --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-primary dark:text-white flex items-center gap-2">
                        <span>Pekerjaan Unit Harian (Daily Units M01–M12)</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Daftar unit yang telah dikerjakan beserta status submodul & nilai Final Mission
                    </p>
                </div>
            </div>

            <x-admin.table.wrapper>
                <table class="min-w-full divide-y divide-slate-200 dark:divide-neutral-700">
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
                    <tbody class="divide-y divide-slate-200 dark:divide-neutral-700">
                        @php
                            $dailyUnits = collect($modules)->filter(fn($m) => !$m->is_exam && !str_ends_with($m->module_id, '-M13'));
                        @endphp
                        @forelse($dailyUnits as $unit)
                            @php
                                $subs = collect($unit->sub_modules ?? []);
                                $assessment = $subs->first(fn($s) => $s->skill_type === 'assessment' || $s->skill_type === 'exam');
                            @endphp
                            <x-admin.table.tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold text-slate-700 dark:text-neutral-300">
                                        {{ $unit->module_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-heading font-bold text-sm text-primary dark:text-white block">{{ $unit->title }}</span>
                                    <span class="text-xs text-slate-400">Level {{ $unit->level_id }} · CEFR {{ $unit->cefr }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        @foreach($subs as $sub)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $sub->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-surface text-slate-600 border border-slate-200 dark:bg-neutral-800 dark:text-neutral-400' }}"
                                                  title="{{ $sub->title }} ({{ $sub->status }})">
                                                {{ ucfirst(substr($sub->skill_type, 0, 4)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($assessment && !is_null($assessment->score))
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg {{ $assessment->score >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $assessment->score }}
                                        </span>
                                    @elseif(!is_null($unit->score))
                                        <span class="text-xs font-bold text-slate-800 dark:text-neutral-200">
                                            {{ $unit->score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($unit->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Dalam Proses</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-end whitespace-nowrap">
                                    <span class="text-xs text-slate-500 dark:text-neutral-400">
                                        {{ $unit->completed_at ? \Carbon\Carbon::parse($unit->completed_at)->translatedFormat('d M Y, H:i') : '-' }}
                                    </span>
                                </td>
                            </x-admin.table.tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 italic">
                                    Belum ada pekerjaan unit harian yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-admin.table.wrapper>
        </div>
    </div>
@endsection
