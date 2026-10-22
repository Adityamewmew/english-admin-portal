@extends('_admin._layout.app')

@section('title', 'Add New Sub Module')

@section('content')
    <div class="max-w-2xl">
        <x-admin.card variant="white" class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-neutral-700/80 flex items-center gap-x-3 bg-surface/50 dark:bg-neutral-800/50">
                <a href="{{ route('admin.sub_modules.index') }}"
                    class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                    title="Kembali ke Daftar Sub-Modul">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-heading font-extrabold text-lg text-primary dark:text-white tracking-tight">Add Sub Module</h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">Assign a lesson or sub-skill to a curriculum module</p>
                </div>
            </div>

            <form class="p-6 space-y-4" action="{{ route('admin.sub_modules.create') }}" method="POST">
                @csrf

                @php
                    $modOptions = ['' => '-- Choose Module --'];
                    foreach ($modules as $mod) {
                        $modOptions[$mod->id] = "[{$mod->cefr}] {$mod->title} ({$mod->id})";
                    }
                @endphp

                <x-admin.select
                    label="Parent Curriculum Module"
                    name="module_id"
                    required
                    :value="old('module_id', $selectedModule)"
                    :options="$modOptions"
                    error="{{ $errors->first('module_id') }}"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-admin.select
                        label="Skill Category"
                        name="skill_type"
                        required
                        :value="old('skill_type')"
                        :options="['speaking' => 'Speaking', 'listening' => 'Listening', 'reading' => 'Reading', 'grammar' => 'Grammar', 'vocabulary' => 'Vocabulary', 'writing' => 'Writing']"
                    />

                    <x-admin.input
                        label="Order Sequence"
                        name="order_index"
                        type="number"
                        value="{{ old('order_index', 1) }}"
                        min="1"
                        required
                    />
                </div>

                <x-admin.input
                    label="Sub Module Title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="e.g. Dialogue Practice: At the Airport"
                    required
                    error="{{ $errors->first('title') }}"
                />

                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700 flex justify-end gap-x-2">
                    <x-admin.button href="{{ route('admin.sub_modules.index') }}" variant="outline" as="a" size="sm">
                        Cancel
                    </x-admin.button>
                    <x-admin.button type="submit" variant="secondary" size="sm">
                        Save Sub Module
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
