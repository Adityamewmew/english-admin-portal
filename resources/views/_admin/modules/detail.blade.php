@extends('_admin._layout.app')

@section('title', 'Detail Modul: ' . $module->title)

@section('content')
    <div class="space-y-6">
        {{-- Header Card --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <a href="{{ route('admin.modules.index') }}"
                        class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded">
                                {{ $module->id }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                CEFR {{ $module->cefr }}
                            </span>
                            @if (!empty($module->is_exam))
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    Ujian Kelulusan
                                </span>
                            @endif
                        </div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $module->title }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Tujuan Pembelajaran (Objective)</span>
                    <p class="text-sm text-gray-700 dark:text-neutral-300 bg-gray-50 dark:bg-neutral-900/50 p-3.5 rounded-xl border border-gray-100 dark:border-neutral-700">
                        {{ $module->objective }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-400 block mb-0.5">Level Kurikulum</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $module->level_title ?? $module->level_id }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-400 block mb-0.5">Estimasi Waktu</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">±{{ $module->estimated_minutes }} Menit</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-400 block mb-0.5">Passing Score</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $module->passing_score }}%</span>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700">
                        <span class="text-xs text-gray-400 block mb-0.5">Total Bagian Materi</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ count($sections) }} Bagian</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sections List --}}
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                Struktur & Isi Bagian Materi ({{ count($sections) }})
            </h2>

            <div class="space-y-4">
                @forelse($sections as $index => $sec)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                        <div class="px-5 py-3.5 bg-gray-50/70 dark:bg-neutral-900/40 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="size-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $sec->title }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono uppercase font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $sec->section_type }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            @if ($sec->section_type === 'vocabulary' && !empty($sec->content['words']))
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($sec->content['words'] as $w)
                                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700 text-xs">
                                            <div class="font-bold text-sm text-blue-600 dark:text-blue-400">{{ $w['word'] ?? '' }}</div>
                                            <div class="text-gray-500 italic mb-1">{{ $w['phonetic'] ?? '' }} ({{ $w['partOfSpeech'] ?? '' }})</div>
                                            <div class="font-medium text-gray-800 dark:text-neutral-200">{{ $w['meaningId'] ?? '' }}</div>
                                            @if (!empty($w['exampleSentence']))
                                                <div class="text-gray-600 dark:text-neutral-400 mt-1 text-[11px]">"{{ $w['exampleSentence'] }}"</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($sec->section_type === 'dialogue' && !empty($sec->content['lines']))
                                <div class="space-y-2">
                                    @foreach ($sec->content['lines'] as $line)
                                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs flex gap-3">
                                            <span class="font-bold text-blue-600 dark:text-blue-400 min-w-20">{{ $line['speaker'] ?? 'Speaker' }}:</span>
                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-neutral-200">{{ $line['text'] ?? '' }}</div>
                                                @if (!empty($line['translation']))
                                                    <div class="text-gray-400 text-[11px] mt-0.5">{{ $line['translation'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($sec->section_type === 'quiz' && !empty($sec->content['questions']))
                                <div class="space-y-3">
                                    @foreach ($sec->content['questions'] as $qIdx => $q)
                                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs space-y-1.5">
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                {{ $qIdx + 1 }}. {{ $q['question'] ?? '' }}
                                            </div>
                                            @if (!empty($q['options']))
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pl-3">
                                                    @foreach ($q['options'] as $opt)
                                                        <div class="px-2 py-1 rounded {{ ($opt === ($q['answer'] ?? '')) ? 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300' : 'bg-white dark:bg-neutral-800 text-gray-700 dark:text-neutral-300' }}">
                                                            {{ $opt }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <pre class="p-3 bg-gray-900 text-green-400 rounded-xl text-xs overflow-x-auto font-mono max-h-60">{{ json_encode($sec->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 text-gray-500">
                        Belum ada data bagian materi untuk modul ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
