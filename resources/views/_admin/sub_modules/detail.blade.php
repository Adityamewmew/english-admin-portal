@extends('_admin._layout.app')

@section('title', 'Sub Module - ' . $subModule->title)

@section('content')
    <div class="space-y-6">
        <x-admin.page-header :title="$subModule->title" subtitle="Sub-modul kurikulum pembelajaran">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sub_modules.index') }}"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-xs font-semibold rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('admin.sub_modules.update', $subModule->id) }}"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                    @include('_admin._layout.icons.pencil')
                    Edit Sub-Modul
                </a>
                <form action="{{ route('admin.sub_modules.delete', $subModule->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-modul ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-xs font-semibold rounded-xl bg-rose-600 text-white hover:bg-rose-700 shadow-sm cursor-pointer">
                        @include('_admin._layout.icons.trash')
                        Hapus
                    </button>
                </form>
            </div>
        </x-admin.page-header>

        {{-- Meta Card --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-semibold text-gray-400 block uppercase">Sub-Module ID</span>
                    <span class="text-sm font-mono font-bold text-gray-900 dark:text-white mt-1 block">
                        {{ $subModule->id }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 block uppercase">Skill Category</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold mt-1 bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-400">
                        {{ ucfirst($subModule->skill_type) }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 block uppercase">Parent Module</span>
                    <a href="{{ route('admin.modules.detail', $subModule->module_id) }}" class="text-sm font-semibold text-blue-600 hover:underline mt-1 block">
                        [{{ $subModule->module_cefr }}] {{ $subModule->module_title }}
                    </a>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 block uppercase">Urutan</span>
                    <span class="text-sm font-mono font-bold text-gray-900 dark:text-white mt-1 block">
                        #{{ $subModule->order_index }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Sections List --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                Bagian Materi Pembelajaran ({{ count($sections) }})
            </h3>

            @forelse ($sections as $sec)
                <div class="p-4 rounded-xl border border-gray-100 dark:border-neutral-700/60 bg-gray-50/50 dark:bg-neutral-900/40 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            #{{ $sec->order_index }} - {{ $sec->title }}
                        </span>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400">
                            {{ $sec->section_type }}
                        </span>
                    </div>
                    @if (!empty($sec->content))
                        <pre class="text-xs bg-white dark:bg-neutral-900 p-3 rounded-lg border border-gray-200 dark:border-neutral-700 font-mono text-gray-700 dark:text-neutral-300 overflow-x-auto max-h-48">{{ json_encode($sec->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <x-admin.empty-state text="Belum ada bagian materi di dalam sub-modul ini" />
                </div>
            @endforelse
        </div>
    </div>
@endsection
