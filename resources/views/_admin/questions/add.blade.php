@extends('_admin._layout.app')

@section('title', 'Add Question - Placement Test')

@section('content')
    <div class="max-w-3xl">
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
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Add Question Item</h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Add a new item to placement test question bank</p>
                </div>
            </div>

            <form class="p-6 space-y-5" action="{{ route('admin.questions.create') }}" method="POST">
                @csrf

                {{-- CEFR, Skill, Type --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cefr" class="block text-sm font-medium mb-1 dark:text-neutral-200">CEFR Level <span class="text-rose-500">*</span></label>
                        <select id="cefr" name="cefr"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
                                <option value="{{ $lvl }}" {{ old('cefr') == $lvl ? 'selected' : '' }}>Level {{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="skill" class="block text-sm font-medium mb-1 dark:text-neutral-200">Skill <span class="text-rose-500">*</span></label>
                        <select id="skill" name="skill"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach (['grammar' => 'Grammar', 'vocabulary' => 'Vocabulary', 'reading' => 'Reading', 'listening' => 'Listening', 'speaking' => 'Speaking'] as $k => $v)
                                <option value="{{ $k }}" {{ old('skill') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium mb-1 dark:text-neutral-200">Type <span class="text-rose-500">*</span></label>
                        <select id="type" name="type"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="MCQ" {{ old('type', 'MCQ') == 'MCQ' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                            <option value="open_ended" {{ old('type') == 'open_ended' ? 'selected' : '' }}>Open Ended / Writing</option>
                            <option value="speaking" {{ old('type') == 'speaking' ? 'selected' : '' }}>Speaking</option>
                        </select>
                    </div>
                </div>

                {{-- Custom Question ID --}}
                <div>
                    <label for="id" class="block text-sm font-medium mb-1 dark:text-neutral-200">
                        Item ID <span class="text-xs font-normal text-gray-500">(Optional, e.g. Q-A1-001. Leave blank to auto-generate)</span>
                    </label>
                    <input type="text" id="id" name="id" value="{{ old('id') }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 font-mono @error('id') border-rose-500 @enderror"
                        placeholder="e.g. Q-A1-001">
                    @error('id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Question Text --}}
                <div>
                    <label for="question" class="block text-sm font-medium mb-1 dark:text-neutral-200">Question / Prompt <span class="text-rose-500">*</span></label>
                    <textarea id="question" name="question" rows="3"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('question') border-rose-500 @enderror"
                        placeholder="Enter the question sentence, incomplete phrase, or test prompt" required>{{ old('question') }}</textarea>
                    @error('question')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Multiple Choice Options (for MCQ) --}}
                <div id="mcq-options-container" class="space-y-3 p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-200 dark:border-neutral-700">
                    <span class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 block tracking-wider">Multiple Choice Options (A, B, C, D)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (['A', 'B', 'C', 'D'] as $idx => $label)
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-neutral-300 mb-1">Option {{ $label }}</label>
                                <input type="text" name="options[]" value="{{ old('options.' . $idx) }}"
                                    class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Option {{ $label }} text">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Answer --}}
                <div>
                    <label for="answer" class="block text-sm font-medium mb-1 dark:text-neutral-200">Correct Answer <span class="text-rose-500">*</span></label>
                    <input type="text" id="answer" name="answer" value="{{ old('answer') }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('answer') border-rose-500 @enderror"
                        placeholder="Exact correct answer string (must match one of the options for MCQ)" required>
                    @error('answer')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Audio Script & Audio URL (Listening) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="audio_script" class="block text-sm font-medium mb-1 dark:text-neutral-200">Audio Script / Transcript <span class="text-xs font-normal text-gray-500">(Optional)</span></label>
                        <textarea id="audio_script" name="audio_script" rows="2"
                            class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Transcript text for audio narration">{{ old('audio_script') }}</textarea>
                    </div>
                    <div>
                        <label for="audio_url" class="block text-sm font-medium mb-1 dark:text-neutral-200">Audio URL <span class="text-xs font-normal text-gray-500">(Optional)</span></label>
                        <input type="text" id="audio_url" name="audio_url" value="{{ old('audio_url') }}"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="https://.../audio.mp3">
                    </div>
                </div>

                {{-- Explanation --}}
                <div>
                    <label for="explanation" class="block text-sm font-medium mb-1 dark:text-neutral-200">Explanation / Discussion <span class="text-xs font-normal text-gray-500">(Optional)</span></label>
                    <textarea id="explanation" name="explanation" rows="2"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Explanation why this answer is correct">{{ old('explanation') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-neutral-700 flex justify-end gap-x-2">
                    <a href="{{ route('admin.questions.index') }}"
                        class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                        Save Question
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
