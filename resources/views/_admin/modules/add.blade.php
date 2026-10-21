@extends('_admin._layout.app')

@section('title', 'Add New Module')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center gap-x-3">
                <a href="{{ route('admin.modules.index') }}"
                    class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Add Curriculum Module</h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Create a new CEFR curriculum module</p>
                </div>
            </div>

            <form class="p-6 space-y-4" action="{{ route('admin.modules.create') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="id" class="block text-sm font-medium mb-1 dark:text-neutral-200">Module Code / ID <span class="text-xs text-gray-400">(Optional - auto-generated if blank)</span></label>
                        <input type="text" id="id" name="id" value="{{ old('id') }}"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. MOD-A1.1-01">
                        @error('id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cefr" class="block text-sm font-medium mb-1 dark:text-neutral-200">CEFR Level <span class="text-rose-500">*</span></label>
                        <select id="cefr" name="cefr"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $c)
                                <option value="{{ $c }}" {{ old('cefr') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium mb-1 dark:text-neutral-200">Module Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('title') border-rose-500 @enderror"
                        placeholder="e.g. Introducing Yourself & Basic Greetings" required>
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="level_id" class="block text-sm font-medium mb-1 dark:text-neutral-200">Curriculum Sub-Level <span class="text-rose-500">*</span></label>
                        <select id="level_id" name="level_id"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @forelse ($levels as $lvl)
                                <option value="{{ $lvl->id }}" {{ old('level_id') == $lvl->id ? 'selected' : '' }}>
                                    {{ $lvl->id }} - {{ $lvl->title }}
                                </option>
                            @empty
                                @foreach (['A1.1', 'A1.2', 'A2.1', 'A2.2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'C2.1', 'C2.2'] as $l)
                                    <option value="{{ $l }}" {{ old('level_id') == $l ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label for="group" class="block text-sm font-medium mb-1 dark:text-neutral-200">Topic / Category Group <span class="text-rose-500">*</span></label>
                        <input type="text" id="group" name="group" value="{{ old('group', 'Daily Communication') }}"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. Daily Life, Travel, Career" required>
                    </div>
                </div>

                {{-- Banner Image Input (JPG, PNG) --}}
                <div>
                    <label for="banner" class="block text-sm font-medium mb-1 dark:text-neutral-200">
                        Gambar Banner Modul <span class="text-xs text-gray-400 font-normal">(Opsional - format JPG, PNG maks 5MB)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="file" id="banner" name="banner" accept="image/png,image/jpeg,image/jpg"
                            onchange="previewBanner(this)"
                            class="py-2 px-3 block w-full border border-gray-200 rounded-lg text-sm file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950 dark:file:text-blue-300 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 cursor-pointer">
                        <div id="banner-preview-wrap" class="hidden shrink-0 size-16 rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900">
                            <img id="banner-preview" src="#" alt="Preview Banner" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @error('banner')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="objective" class="block text-sm font-medium mb-1 dark:text-neutral-200">Learning Objective</label>
                    <textarea id="objective" name="objective" rows="3"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Explain what students will master in this module...">{{ old('objective') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="complexity" class="block text-sm font-medium mb-1 dark:text-neutral-200">Complexity <span class="text-rose-500">*</span></label>
                        <select id="complexity" name="complexity"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="easy" {{ old('complexity', 'medium') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('complexity', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('complexity', 'medium') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    <div>
                        <label for="estimated_minutes" class="block text-sm font-medium mb-1 dark:text-neutral-200">Est. Duration (Min)</label>
                        <input type="number" id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', 15) }}" min="1"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="passing_score" class="block text-sm font-medium mb-1 dark:text-neutral-200">Passing Score (%)</label>
                        <input type="number" id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" min="0" max="100"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-2">
                    <div>
                        <label for="order_index" class="block text-sm font-medium mb-1 dark:text-neutral-200">Order Sequence</label>
                        <input type="number" id="order_index" name="order_index" value="{{ old('order_index', 1) }}" min="1"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center gap-x-3 pt-6">
                        <input type="checkbox" id="is_exam" name="is_exam" value="1" {{ old('is_exam') ? 'checked' : '' }}
                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 size-4">
                        <label for="is_exam" class="text-sm font-medium dark:text-neutral-200">
                            Mark as Checkpoint / Exam Module
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-neutral-700 flex justify-end gap-x-2">
                    <a href="{{ route('admin.modules.index') }}"
                        class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                        Save Module
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewBanner(input) {
            const previewWrap = document.getElementById('banner-preview-wrap');
            const preview = document.getElementById('banner-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewWrap.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
