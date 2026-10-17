@extends('_admin._layout.app')

@section('title', 'Detail Butir Soal: ' . $item->id)

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center gap-x-3">
                <a href="{{ route('admin.questions.index') }}"
                    class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded">
                            {{ $item->id }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                            CEFR {{ $item->cefr }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold capitalize bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                            {{ $item->skill }}
                        </span>
                    </div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                        Detail Butir Soal
                    </h1>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Question text --}}
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Pertanyaan</span>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-200 dark:border-neutral-700 text-base font-medium text-gray-900 dark:text-white">
                        {{ $item->question }}
                    </div>
                </div>

                {{-- Multiple Choice Options --}}
                @if (!empty($item->options) && is_array($item->options))
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Pilihan Jawaban (Options)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach ($item->options as $opt)
                                @php
                                    $isCorrect = trim($opt) === trim($item->answer);
                                @endphp
                                <div class="p-3 rounded-xl border text-sm flex items-center justify-between {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-900 dark:bg-emerald-950/40 dark:border-emerald-700 dark:text-emerald-200 font-bold' : 'bg-white dark:bg-neutral-900 border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-neutral-300' }}">
                                    <span>{{ $opt }}</span>
                                    @if ($isCorrect)
                                        <span class="text-xs px-2 py-0.5 rounded bg-emerald-600 text-white font-semibold">Kunci</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Audio Script if any --}}
                @if (!empty($item->audio_script))
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Transkrip Audio (Listening)</span>
                        <div class="p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 text-xs text-amber-900 dark:text-amber-200 italic">
                            "{{ $item->audio_script }}"
                        </div>
                    </div>
                @endif

                {{-- Explanation --}}
                @if (!empty($item->explanation))
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Penjelasan / Pembahasan</span>
                        <div class="p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800/40 text-xs text-blue-900 dark:text-blue-200">
                            {{ $item->explanation }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
