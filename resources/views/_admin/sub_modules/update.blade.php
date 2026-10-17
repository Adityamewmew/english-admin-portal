@extends('_admin._layout.app')

@section('title', 'Edit Sub Module - ' . $subModule->title)

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center gap-x-3">
                <a href="{{ route('admin.sub_modules.index') }}"
                    class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Edit Sub Module</h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">ID: {{ $subModule->id }}</p>
                </div>
            </div>

            <form class="p-6 space-y-4" action="{{ route('admin.sub_modules.doUpdate', $subModule->id) }}" method="POST">
                @csrf

                <div>
                    <label for="module_id" class="block text-sm font-medium mb-1 dark:text-neutral-200">Parent Curriculum Module <span class="text-rose-500">*</span></label>
                    <select id="module_id" name="module_id"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                        @foreach ($modules as $mod)
                            <option value="{{ $mod->id }}" {{ old('module_id', $subModule->module_id) == $mod->id ? 'selected' : '' }}>
                                [{{ $mod->cefr }}] {{ $mod->title }} ({{ $mod->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="skill_type" class="block text-sm font-medium mb-1 dark:text-neutral-200">Skill Category <span class="text-rose-500">*</span></label>
                        <select id="skill_type" name="skill_type"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach (['speaking' => 'Speaking', 'listening' => 'Listening', 'reading' => 'Reading', 'grammar' => 'Grammar', 'vocabulary' => 'Vocabulary', 'writing' => 'Writing'] as $k => $v)
                                <option value="{{ $k }}" {{ old('skill_type', $subModule->skill_type) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="order_index" class="block text-sm font-medium mb-1 dark:text-neutral-200">Order Sequence</label>
                        <input type="number" id="order_index" name="order_index" value="{{ old('order_index', $subModule->order_index) }}" min="1"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium mb-1 dark:text-neutral-200">Sub Module Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $subModule->title) }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('title') border-rose-500 @enderror" required>
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-neutral-700 flex justify-end gap-x-2">
                    <a href="{{ route('admin.sub_modules.index') }}"
                        class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                        Update Sub Module
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
