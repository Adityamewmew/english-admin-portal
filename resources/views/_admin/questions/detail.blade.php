@extends('_admin._layout.app')

@section('title', 'Detail Butir Soal: ' . $item->id)

@section('content')
    <div class="max-w-3xl space-y-6">
        <x-admin.card variant="white" class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-neutral-700/80 flex items-center justify-between gap-x-3 bg-surface/50 dark:bg-neutral-800/50">
                <div class="flex items-center gap-x-3">
                    <a href="{{ route('admin.questions.index') }}"
                        class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                        title="Kembali ke Bank Soal">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-primary dark:text-accent bg-surface dark:bg-neutral-800 px-2 py-0.5 rounded-md border border-slate-300 dark:border-neutral-700">
                                {{ $item->id }}
                            </span>
                            <x-admin.badge type="cefr" :value="$item->cefr" />
                            <span class="px-2 py-0.5 rounded-md text-xs font-heading font-extrabold capitalize bg-surface text-secondary border border-secondary/30">
                                {{ $item->skill }}
                            </span>
                        </div>
                        <h1 class="font-heading font-extrabold text-lg text-primary dark:text-white mt-1">
                            Detail Butir Soal
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-admin.button href="{{ route('admin.questions.update', $item->id) }}" variant="secondary" size="sm" as="a">
                        @include('_admin._layout.icons.pencil')
                        Edit
                    </x-admin.button>
                    <form action="{{ route('admin.questions.delete', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus butir soal ini?')">
                        @csrf
                        @method('DELETE')
                        <x-admin.button type="submit" variant="danger" size="sm">
                            @include('_admin._layout.icons.trash')
                            Hapus
                        </x-admin.button>
                    </form>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Question text --}}
                <div>
                    <span class="text-xs font-heading font-extrabold text-primary/70 uppercase tracking-wider block mb-1.5">Pertanyaan</span>
                    <div class="p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 text-base font-semibold text-primary dark:text-white card-tactile leading-relaxed">
                        {{ $item->question }}
                    </div>
                </div>

                {{-- Multiple Choice Options --}}
                @if (!empty($item->options) && is_array($item->options))
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary/70 uppercase tracking-wider block mb-2">Pilihan Jawaban (Options)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach ($item->options as $opt)
                                @php
                                    $isCorrect = trim($opt) === trim($item->answer);
                                @endphp
                                <div class="p-3.5 rounded-xl border text-sm flex items-center justify-between {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-900 dark:bg-emerald-950/40 dark:border-emerald-700 dark:text-emerald-200 font-bold shadow-2xs' : 'bg-white dark:bg-neutral-900 border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-neutral-300' }}">
                                    <span>{{ $opt }}</span>
                                    @if ($isCorrect)
                                        <span class="text-xs px-2 py-0.5 rounded bg-emerald-600 text-white font-bold uppercase tracking-wider">Kunci</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Audio Script if any --}}
                @if (!empty($item->audio_script))
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary/70 uppercase tracking-wider block mb-1.5">Transkrip Audio (Listening)</span>
                        <div class="p-3.5 rounded-xl bg-surface border border-secondary/30 dark:bg-neutral-900/40 text-xs text-primary dark:text-neutral-200 italic font-medium leading-relaxed">
                            "{{ $item->audio_script }}"
                        </div>
                    </div>
                @endif

                {{-- Explanation --}}
                @if (!empty($item->explanation))
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary/70 uppercase tracking-wider block mb-1.5">Penjelasan / Pembahasan</span>
                        <div class="p-3.5 rounded-xl bg-surface border border-slate-200 dark:bg-neutral-900/40 text-xs text-slate-700 dark:text-neutral-300 leading-relaxed font-medium">
                            {{ $item->explanation }}
                        </div>
                    </div>
                @endif
            </div>
        </x-admin.card>
    </div>
@endsection
