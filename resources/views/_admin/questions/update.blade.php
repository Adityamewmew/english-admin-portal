@extends('_admin._layout.app')

@section('title', 'Edit Question - ' . $item->id)

@section('content')
    <div class="max-w-3xl">
        <x-admin.card variant="white" class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-neutral-700/80 flex items-center gap-x-3 bg-surface/50 dark:bg-neutral-800/50">
                <a href="{{ route('admin.questions.detail', $item->id) }}"
                    class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                    title="Kembali ke Detail Butir Soal">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-heading font-extrabold text-lg text-primary dark:text-white tracking-tight">Edit Question Item</h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">Update item details for ID: <span class="font-mono font-bold text-primary dark:text-accent">{{ $item->id }}</span></p>
                </div>
            </div>

            <form class="p-6 space-y-5" action="{{ route('admin.questions.doUpdate', $item->id) }}" method="POST">
                @csrf

                {{-- CEFR, Skill, Type --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-admin.select
                        label="CEFR Level"
                        name="cefr"
                        required
                        :value="old('cefr', $item->cefr)"
                        :options="['A1' => 'Level A1', 'A2' => 'Level A2', 'B1' => 'Level B1', 'B2' => 'Level B2', 'C1' => 'Level C1', 'C2' => 'Level C2']"
                    />

                    <x-admin.select
                        label="Skill"
                        name="skill"
                        required
                        :value="old('skill', $item->skill)"
                        :options="['mcq' => 'MCQ (Grammar & Vocabulary)', 'listening' => 'Listening', 'speaking' => 'Speaking', 'writing' => 'Writing', 'grammar' => 'Grammar', 'vocabulary' => 'Vocabulary', 'reading' => 'Reading']"
                    />

                    <x-admin.select
                        label="Type"
                        name="type"
                        required
                        :value="old('type', $item->type)"
                        :options="['MCQ' => 'Multiple Choice (MCQ)', 'PROMPT' => 'Prompt (Writing / Speaking)', 'open_ended' => 'Open Ended / Writing', 'speaking' => 'Speaking']"
                    />
                </div>

                {{-- Question Text --}}
                <div>
                    <label for="question" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                        Question / Prompt <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="question" name="question" rows="3"
                        class="py-2.5 px-3.5 block w-full bg-white border border-slate-300 rounded-[10px] text-slate-800 placeholder-slate-400 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 text-sm leading-relaxed @error('question') border-rose-500 @enderror"
                        required>{{ old('question', $item->question) }}</textarea>
                    @error('question')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Multiple Choice Options (for MCQ) --}}
                @php
                    $optionsList = old('options', is_array($item->options) ? $item->options : []);
                @endphp
                <div id="mcq-options-container" class="space-y-3 p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 card-tactile">
                    <span class="text-xs font-heading font-extrabold uppercase text-primary/80 dark:text-neutral-300 block tracking-wider">Multiple Choice Options (A, B, C, D)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (['A', 'B', 'C', 'D'] as $idx => $label)
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-neutral-300 mb-1">Option {{ $label }}</label>
                                <input type="text" name="options[]" value="{{ $optionsList[$idx] ?? '' }}"
                                    class="py-2 px-3 block w-full bg-white border border-slate-300 rounded-[10px] text-sm text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white"
                                    placeholder="Option {{ $label }} text">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Answer --}}
                <x-admin.input
                    label="Correct Answer"
                    name="answer"
                    value="{{ old('answer', $item->answer) }}"
                    placeholder="Exact correct answer string"
                    required
                    error="{{ $errors->first('answer') }}"
                />

                {{-- Audio Script & Audio URL (Listening) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="audio_script" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Audio Script / Transcript <span class="text-xs font-normal text-slate-400">(Optional)</span>
                        </label>
                        <textarea id="audio_script" name="audio_script" rows="2"
                            class="py-2 px-3 block w-full bg-white border border-slate-300 rounded-[10px] text-sm text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white"
                            placeholder="Transcript text for audio narration">{{ old('audio_script', $item->audio_script) }}</textarea>
                    </div>
                    <div>
                        <x-admin.input
                            label="Audio URL (Optional)"
                            name="audio_url"
                            value="{{ old('audio_url', $item->audio_url) }}"
                            placeholder="https://.../audio.mp3"
                        />
                    </div>
                </div>

                {{-- Explanation --}}
                <div>
                    <label for="explanation" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                        Explanation / Discussion <span class="text-xs font-normal text-slate-400">(Optional)</span>
                    </label>
                    <textarea id="explanation" name="explanation" rows="2"
                        class="py-2.5 px-3.5 block w-full bg-white border border-slate-300 rounded-[10px] text-sm text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white leading-relaxed"
                        placeholder="Explanation why this answer is correct">{{ old('explanation', $item->explanation) }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700 flex justify-end gap-x-2">
                    <x-admin.button href="{{ route('admin.questions.detail', $item->id) }}" variant="outline" as="a" size="sm">
                        Cancel
                    </x-admin.button>
                    <x-admin.button type="submit" variant="secondary" size="sm">
                        Save Changes
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
