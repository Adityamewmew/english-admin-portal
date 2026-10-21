@props(['sec', 'index', 'module'])

@php
    $typeLabels = [
        'theory' => ['label' => 'Teori & Ringkasan', 'badge' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800'],
        'vocab' => ['label' => 'Kosakata (Vocab)', 'badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'],
        'dialogue' => ['label' => 'Dialog & Bacaan', 'badge' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800'],
        'practice' => ['label' => 'Latihan Praktik', 'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800'],
        'quiz' => ['label' => 'Kuis & Evaluasi', 'badge' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800'],
    ];
    $typeInfo = $typeLabels[$sec->section_type] ?? ['label' => strtoupper($sec->section_type), 'badge' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200'];
    $content = is_array($sec->content) ? $sec->content : (json_decode($sec->content, true) ?: []);
@endphp

<x-admin.card fit="true" id="section-card-{{ $sec->id }}" class="transition-all hover:border-gray-300 dark:hover:border-neutral-600">
    {{-- Card Header --}}
    <div class="px-3 py-2 bg-gray-50/80 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700/80 flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <span class="size-7 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">
                {{ $index }}
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ $sec->title }}
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $typeInfo['badge'] }}">
                        {{ $typeInfo['label'] }}
                    </span>
                </div>
                <div class="text-[11px] text-gray-400 font-mono mt-0.5">
                    ID: {{ $sec->id }} &bull; Urutan: #{{ $sec->order_index }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-admin.button type="button"
                size="sm"
                color="outline-secondary"
                onclick='openEditSectionModal(@json($sec))'
                class="text-xs">
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Edit Materi
            </x-admin.button>
            <x-admin.button type="button"
                size="sm"
                color="outline-danger"
                onclick="confirmDeleteSection('{{ $sec->id }}', '{{ addslashes($sec->title) }}', '{{ $sec->sub_module_id }}')"
                class="text-xs">
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18"/>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                </svg>
                Hapus
            </x-admin.button>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="p-5 space-y-4">
        {{-- 1. VOCABULARY SECTION --}}
        @if ($sec->section_type === 'vocab')
            @php $words = $content['items'] ?? $content['words'] ?? []; @endphp
            @if (!empty($words))
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                        <svg class="size-3.5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>
                        Daftar Kosakata ({{ count($words) }} Kata)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($words as $w)
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700/80 space-y-1.5">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="font-bold text-sm text-blue-600 dark:text-blue-400">{{ $w['word'] ?? '-' }}</span>
                                    @if (!empty($w['partOfSpeech']))
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-200/70 text-gray-700 dark:bg-neutral-700 dark:text-neutral-300">
                                            {{ $w['partOfSpeech'] }}
                                        </span>
                                    @endif
                                </div>
                                @if (!empty($w['phonetic']))
                                    <div class="text-gray-400 font-mono text-[11px]">{{ $w['phonetic'] }}</div>
                                @endif
                                <div class="text-xs font-medium text-gray-800 dark:text-neutral-200">
                                    {{ $w['meaningId'] ?? $w['meaning'] ?? '-' }}
                                </div>
                                @if (!empty($w['exampleSentence']) || !empty($w['example']))
                                    <div class="text-[11px] text-gray-600 dark:text-neutral-400 bg-white dark:bg-neutral-800 p-2 rounded-lg border border-gray-100 dark:border-neutral-700/50 italic">
                                        "{{ $w['exampleSentence'] ?? $w['example'] }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-xs text-gray-400 italic">Belum ada daftar kata pada bagian kosakata ini.</div>
            @endif

        {{-- 2. DIALOGUE & PASSAGE SECTION --}}
        @elseif ($sec->section_type === 'dialogue')
            @if (!empty($content['context']))
                <div class="p-3 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 text-xs text-blue-800 dark:text-blue-300">
                    <span class="font-bold">Konteks:</span> {{ $content['context'] }}
                </div>
            @endif

            @if (!empty($content['lines']))
                <div class="space-y-2.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="size-3.5 text-purple-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Percakapan ({{ count($content['lines']) }} Baris)
                    </div>
                    @foreach ($content['lines'] as $line)
                        <div class="p-3 rounded-xl bg-gray-50/70 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700/80 text-xs flex gap-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400 shrink-0 w-24">
                                {{ $line['speaker'] ?? 'Speaker' }}
                            </span>
                            <div class="space-y-0.5">
                                <div class="font-medium text-gray-900 dark:text-neutral-100">{{ $line['text'] ?? '' }}</div>
                                @if (!empty($line['translation']))
                                    <div class="text-gray-500 dark:text-neutral-400 text-[11px]">{{ $line['translation'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (!empty($content['text']))
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Teks Bacaan</span>
                        @if (!empty($content['wordCount']))
                            <span class="text-[11px] font-mono text-gray-400">{{ $content['wordCount'] }} Kata</span>
                        @endif
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs text-gray-800 dark:text-neutral-200 leading-relaxed whitespace-pre-line">
                        {{ $content['text'] }}
                    </div>
                </div>
            @endif

        {{-- 3. QUIZ & PRACTICE SECTION --}}
        @elseif ($sec->section_type === 'quiz' || $sec->section_type === 'practice')
            @php 
                $questions = $content['questions'] ?? []; 
                $practicum = $content['practicumQuestions'] ?? $content['detailQuestions'] ?? [];
            @endphp

            @if (!empty($content['passingScore']))
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 text-xs font-medium border border-emerald-100 dark:border-emerald-800">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                    Passing Score: {{ $content['passingScore'] }}%
                </div>
            @endif

            @if (!empty($questions))
                <div class="space-y-3">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Daftar Soal Kuis ({{ count($questions) }} Soal)
                    </div>
                    @foreach ($questions as $qIdx => $q)
                        <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 space-y-2.5 text-xs">
                            <div class="font-bold text-gray-900 dark:text-white flex items-start gap-2">
                                <span class="size-5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $qIdx + 1 }}
                                </span>
                                <span>{{ $q['question'] ?? '' }}</span>
                            </div>

                            @if (!empty($q['options']))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-7">
                                    @foreach ($q['options'] as $opt)
                                        @php $isCorrect = ($opt === ($q['answer'] ?? '')); @endphp
                                        <div class="px-3 py-1.5 rounded-lg text-xs flex items-center justify-between {{ $isCorrect ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-700' : 'bg-white dark:bg-neutral-800 text-gray-700 dark:text-neutral-300 border border-gray-100 dark:border-neutral-700' }}">
                                            <span>{{ $opt }}</span>
                                            @if ($isCorrect)
                                                <svg class="size-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (!empty($q['explanation']))
                                <div class="pl-7 text-[11px] text-gray-500 dark:text-neutral-400 italic">
                                    Pembahasan: {{ $q['explanation'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if (!empty($practicum))
                <div class="space-y-2.5">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Tugas Interaktif & Praktikum ({{ count($practicum) }} Tugas)
                    </div>
                    @foreach ($practicum as $pIdx => $p)
                        <div class="p-3.5 rounded-xl bg-gray-50/70 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs space-y-1.5">
                            <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    Tugas #{{ $pIdx + 1 }}
                                </span>
                                <span>{{ $p['typeLabel'] ?? ($p['category'] ?? 'Tugas') }}</span>
                            </div>
                            <div class="text-gray-700 dark:text-neutral-300">{{ $p['instruction'] ?? ($p['question'] ?? '') }}</div>
                            @if (!empty($p['targetSentence']))
                                <div class="p-2 rounded bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 font-mono text-[11px] text-blue-600 dark:text-blue-400">
                                    Target: {{ $p['targetSentence'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        {{-- 4. THEORY SECTION --}}
        @elseif ($sec->section_type === 'theory')
            @if (!empty($content['summary']))
                <div class="p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 text-xs">
                    <span class="font-bold text-blue-900 dark:text-blue-300 block mb-1">Ringkasan Materi:</span>
                    <p class="text-blue-800 dark:text-blue-200 leading-relaxed">{{ $content['summary'] }}</p>
                </div>
            @endif

            @if (!empty($content['learningObjectives']))
                <div class="space-y-1.5">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Target Belajar</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($content['learningObjectives'] as $obj)
                            <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs flex items-center gap-2 text-gray-700 dark:text-neutral-300">
                                <svg class="size-4 text-emerald-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ $obj }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($content['rules']))
                <div class="space-y-1.5">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Aturan & Panduan Tata Bahasa</span>
                    <div class="space-y-1.5">
                        @foreach ($content['rules'] as $rIdx => $rule)
                            <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs flex items-start gap-2.5 text-gray-800 dark:text-neutral-200">
                                <span class="size-4.5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $rIdx + 1 }}
                                </span>
                                <span class="leading-relaxed">{{ is_array($rule) ? json_encode($rule) : $rule }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($content['commonTrap']))
                @php $trap = $content['commonTrap']; @endphp
                <div class="p-3.5 rounded-xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 text-xs space-y-2">
                    <div class="font-bold text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
                        <svg class="size-4 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>{{ $trap['trapTitle'] ?? 'Kesalahan Pemula yang Sering Terjadi (Common Trap)' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @if (!empty($trap['wrong']))
                            <div class="p-2.5 rounded-lg bg-white dark:bg-neutral-800 border border-red-200 dark:border-red-900/40 text-red-700 dark:text-red-400">
                                <span class="font-bold block text-[11px] text-red-600 mb-0.5">❌ Sering Salah:</span>
                                "{{ $trap['wrong'] }}"
                            </div>
                        @endif
                        @if (!empty($trap['correct']))
                            <div class="p-2.5 rounded-lg bg-white dark:bg-neutral-800 border border-emerald-200 dark:border-emerald-900/40 text-emerald-700 dark:text-emerald-400">
                                <span class="font-bold block text-[11px] text-emerald-600 mb-0.5">✅ Yang Benar:</span>
                                "{{ $trap['correct'] }}"
                            </div>
                        @endif
                    </div>
                    @if (!empty($trap['explanation']))
                        <div class="text-[11px] text-amber-800 dark:text-amber-300/80 leading-relaxed pt-1 border-t border-amber-200/60 dark:border-amber-900/30">
                            {{ $trap['explanation'] }}
                        </div>
                    @endif
                </div>
            @endif

        {{-- 5. FALLBACK / OTHER --}}
        @else
            <div class="space-y-2">
                @foreach ($content as $key => $val)
                    <div class="p-3 rounded-xl bg-gray-50/70 dark:bg-neutral-900/40 border border-gray-100 dark:border-neutral-700 text-xs">
                        <span class="font-semibold text-gray-500 uppercase text-[10px] block mb-1">{{ Str::headline($key) }}</span>
                        @if (is_array($val))
                            <div class="space-y-1">
                                @foreach ($val as $subK => $subV)
                                    <div class="text-gray-800 dark:text-neutral-200">
                                        &bull; {{ is_array($subV) ? json_encode($subV) : $subV }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-800 dark:text-neutral-200">{{ $val }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-admin.card>

